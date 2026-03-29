<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MailtrapBulkService
{
    private string $baseUrl = 'https://bulk.api.mailtrap.io';

    private string $token;

    private string $fromEmail;

    private string $fromName;

    public function __construct()
    {
        $this->token = config('services.mailtrap.api_token');
        $this->fromEmail = config('services.mailtrap.from_email', 'newsletter@cloudherder.nz');
        $this->fromName = config('services.mailtrap.from_name', 'CloudHerder');
    }

    /**
     * Send bulk emails via Mailtrap Bulk API
     *
     * @param  string  $subject  Email subject
     * @param  string  $htmlContent  HTML email content
     * @param  string  $textContent  Plain text email content
     * @param  array  $recipients  Array of ['email' => string, 'name' => string|null] objects
     * @param  array  $variables  Optional custom variables per recipient
     * @return array ['success' => bool, 'batch_id' => string|null, 'error' => string|null]
     */
    public function sendBulk(
        string $subject,
        string $htmlContent,
        string $textContent,
        array $recipients,
        array $variables = []
    ): array {
        if (empty($this->token)) {
            Log::error('Mailtrap bulk token not configured');

            return ['success' => false, 'batch_id' => null, 'error' => 'Mailtrap token not configured'];
        }

        if (count($recipients) === 0) {
            return ['success' => false, 'batch_id' => null, 'error' => 'No recipients provided'];
        }

        // Mailtrap supports up to 1000 recipients per request
        if (count($recipients) > 1000) {
            Log::warning('Recipient count exceeds 1000, consider batching', [
                'recipient_count' => count($recipients),
            ]);
        }

        $to = array_map(function ($recipient) {
            return [
                'email' => $recipient['email'],
                'name' => $recipient['name'] ?? null,
            ];
        }, $recipients);

        $payload = [
            'from' => [
                'email' => $this->fromEmail,
                'name' => $this->fromName,
            ],
            'to' => $to,
            'subject' => $subject,
            'text' => $textContent,
            'html' => $htmlContent,
            'category' => 'newsletter',
        ];

        // Add custom variables if provided
        if (! empty($variables)) {
            $payload['custom_variables'] = $variables;
        }

        try {
            $response = Http::withHeaders([
                'Api-Token' => $this->token,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/api/send", $payload);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'batch_id' => $data['message_ids'][0] ?? null,
                    'error' => null,
                ];
            }

            $error = $response->json('message') ?? $response->body() ?? 'Unknown error';
            Log::error('Mailtrap bulk send failed', [
                'status' => $response->status(),
                'error' => $error,
            ]);

            return ['success' => false, 'batch_id' => null, 'error' => $error];
        } catch (\Exception $e) {
            Log::error('Mailtrap bulk send exception', [
                'message' => $e->getMessage(),
            ]);

            return ['success' => false, 'batch_id' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a single test email
     */
    public function sendTest(
        string $subject,
        string $htmlContent,
        string $textContent,
        string $toEmail,
        ?string $toName = null
    ): array {
        return $this->sendBulk(
            $subject,
            $htmlContent,
            $textContent,
            [['email' => $toEmail, 'name' => $toName]]
        );
    }

    /**
     * Send emails in batches if recipients exceed 1000
     *
     * @return array Array of batch results
     */
    public function sendBulkBatched(
        string $subject,
        string $htmlContent,
        string $textContent,
        array $recipients,
        int $batchSize = 1000
    ): array {
        $batches = array_chunk($recipients, $batchSize);
        $results = [];

        foreach ($batches as $batchRecipients) {
            $result = $this->sendBulk($subject, $htmlContent, $textContent, $batchRecipients);
            $results[] = $result;

            // Small delay between batches to respect rate limits
            if (count($batches) > 1) {
                usleep(100000); // 100ms
            }
        }

        return $results;
    }
}
