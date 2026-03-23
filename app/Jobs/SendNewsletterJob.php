<?php

namespace App\Jobs;

use App\Models\NewsletterActivity;
use App\Models\NewsletterSubscriber;
use App\Services\MailtrapBulkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 600; // 10 minutes for large sends

    public function __construct(
        public int $activityId
    ) {}

    public function handle(MailtrapBulkService $mailtrap): void
    {
        $activity = NewsletterActivity::with(['newsletterPost.posts'])->find($this->activityId);

        if (! $activity) {
            Log::error('Newsletter activity not found', ['activity_id' => $this->activityId]);

            return;
        }

        // Skip if already sent or cancelled
        if ($activity->isSent() || $activity->isCancelled()) {
            return;
        }

        $activity->markAsSending();

        try {
            $newsletterPost = $activity->newsletterPost;
            $post = $newsletterPost->posts()->first();

            if (! $post) {
                throw new \Exception('Newsletter post has no associated post');
            }

            // Build email content
            $subject = $post->title;
            $trackingPixelUrl = route('newsletter.track-open', ['uuid' => $newsletterPost->id]);

            // Render HTML email
            $htmlContent = $this->renderHtmlEmail($post, $newsletterPost, $trackingPixelUrl);

            // Render plain text email
            $textContent = $this->renderTextEmail($post, $newsletterPost);

            if ($activity->is_test && $activity->test_recipients) {
                // Send test emails
                $recipients = array_map(function ($email) {
                    return ['email' => $email, 'name' => null];
                }, $activity->test_recipients);

                $result = $mailtrap->sendBulk($subject, $htmlContent, $textContent, $recipients);

                if ($result['success']) {
                    $activity->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'recipients_count' => count($recipients),
                        'sent_count' => count($recipients),
                        'mailtrap_batch_id' => $result['batch_id'],
                    ]);
                } else {
                    $activity->markAsFailed($result['error'] ?? 'Test send failed');
                }

                return;
            }

            // Get confirmed subscribers
            $subscribers = NewsletterSubscriber::confirmed()->active()->get();
            $recipients = $subscribers->map(function ($subscriber) {
                return [
                    'email' => $subscriber->email,
                    'name' => $subscriber->name,
                ];
            })->toArray();

            if (empty($recipients)) {
                $activity->markAsFailed('No confirmed subscribers found');

                return;
            }

            $activity->update(['recipients_count' => count($recipients)]);

            // Send in batches if needed
            $results = $mailtrap->sendBulkBatched($subject, $htmlContent, $textContent, $recipients);

            // Aggregate results
            $totalSent = 0;
            $totalFailed = 0;
            $batchIds = [];
            $errors = [];

            foreach ($results as $result) {
                $recipientCount = count($recipients);
                $batchSize = $recipientCount >= 1000 ? 1000 : ($recipientCount % 1000 ?: $recipientCount);
                if ($result['success']) {
                    $totalSent += $batchSize;
                    $batchIds[] = $result['batch_id'];
                } else {
                    $totalFailed += $batchSize;
                    $errors[] = $result['error'];
                }
            }

            if ($totalFailed === 0) {
                $activity->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'sent_count' => $totalSent,
                    'mailtrap_batch_id' => implode(',', $batchIds),
                ]);

                // Mark newsletter as sent
                $newsletterPost->markAsSent($totalSent);
            } else {
                $activity->update([
                    'status' => $totalSent > 0 ? 'sent' : 'failed',
                    'sent_at' => $totalSent > 0 ? now() : null,
                    'sent_count' => $totalSent,
                    'failed_count' => $totalFailed,
                    'mailtrap_batch_id' => ! empty($batchIds) ? implode(',', $batchIds) : null,
                    'error_message' => ! empty($errors) ? implode('; ', $errors) : null,
                ]);

                if ($totalSent > 0) {
                    $newsletterPost->markAsSent($totalSent);
                }
            }

        } catch (\Exception $e) {
            Log::error('Newsletter send failed', [
                'activity_id' => $this->activityId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $activity->markAsFailed($e->getMessage());

            throw $e; // Re-throw to trigger retry
        }
    }

    public function failed(\Throwable $exception): void
    {
        $activity = NewsletterActivity::find($this->activityId);

        if ($activity && ! $activity->isSent()) {
            $activity->markAsFailed($exception->getMessage());
        }
    }

    private function renderHtmlEmail($post, $newsletterPost, string $trackingPixelUrl): string
    {
        $unsubscribeUrl = route('newsletter.unsubscribe-web', ['email' => '{{email}}']); // Will be replaced per recipient if needed

        return view('emails.newsletter-html', [
            'post' => $post,
            'newsletterPost' => $newsletterPost,
            'contentHtml' => $post->content_html,
            'trackingPixelUrl' => $trackingPixelUrl,
            'unsubscribeUrl' => $unsubscribeUrl,
            'viewInBrowserUrl' => route('newsletter.show', ['uuid' => $newsletterPost->id]),
            'headerImageUrl' => $newsletterPost->getFirstMediaUrl('header_image'),
        ])->render();
    }

    private function renderTextEmail($post, $newsletterPost): string
    {
        $content = strip_tags($post->content_html ?? $post->content);

        return view('emails.newsletter-text', [
            'post' => $post,
            'newsletterPost' => $newsletterPost,
            'content' => $content,
            'viewInBrowserUrl' => route('newsletter.show', ['uuid' => $newsletterPost->id]),
            'unsubscribeUrl' => route('newsletter.unsubscribe-web'),
        ])->render();
    }
}
