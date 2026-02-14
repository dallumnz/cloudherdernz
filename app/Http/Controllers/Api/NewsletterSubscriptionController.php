<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsletterSubscriptionRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Newsletter Subscription API Controller
 *
 * Handles public newsletter subscription endpoints.
 */
class NewsletterSubscriptionController extends Controller
{
    /**
     * Subscribe to the newsletter.
     *
     * Creates a new subscriber or reactivates an existing one.
     */
    public function subscribe(NewsletterSubscriptionRequest $request): JsonResponse
    {
        $email = $request->validated('email');

        // Check if subscriber already exists
        $subscriber = NewsletterSubscriber::withTrashed()
            ->where('email', $email)
            ->first();

        if ($subscriber) {
            // If soft deleted, restore
            if ($subscriber->trashed()) {
                $subscriber->restore();
            }

            // If already active and confirmed
            if ($subscriber->isActive() && $subscriber->isConfirmed()) {
                return response()->json([
                    'message' => 'You are already subscribed to our newsletter.',
                    'subscriber' => [
                        'email' => $subscriber->email,
                        'status' => $subscriber->status,
                    ],
                ], 200);
            }

            // Update existing subscriber
            $subscriber->update([
                'name' => $request->validated('name') ?? $subscriber->name,
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'preferences' => $request->validated('preferences') ?? $subscriber->preferences,
                'unsubscribed_at' => null,
            ]);
        } else {
            // Create new subscriber
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'name' => $request->validated('name'),
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'preferences' => $request->validated('preferences'),
            ]);
        }

        return response()->json([
            'message' => 'Thank you for subscribing! Please check your email to confirm your subscription.',
            'subscriber' => [
                'id' => $subscriber->id,
                'email' => $subscriber->email,
                'status' => $subscriber->status,
            ],
        ], 201);
    }

    /**
     * Confirm subscription via token.
     */
    public function confirm(string $token): JsonResponse
    {
        $subscriber = NewsletterSubscriber::findByToken($token);

        if (! $subscriber) {
            return response()->json([
                'message' => 'Invalid or expired confirmation token.',
            ], 404);
        }

        if ($subscriber->isConfirmed()) {
            return response()->json([
                'message' => 'Your subscription is already confirmed.',
            ], 200);
        }

        $subscriber->confirm();

        return response()->json([
            'message' => 'Your subscription has been confirmed. Thank you!',
            'subscriber' => [
                'id' => $subscriber->id,
                'email' => $subscriber->email,
                'status' => $subscriber->status,
                'confirmed_at' => $subscriber->confirmed_at,
            ],
        ], 200);
    }

    /**
     * Unsubscribe from the newsletter.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $email = $request->input('email');

        if (! $email) {
            return response()->json([
                'message' => 'Email address is required.',
            ], 422);
        }

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if (! $subscriber) {
            return response()->json([
                'message' => 'Subscriber not found.',
            ], 404);
        }

        if ($subscriber->isUnsubscribed()) {
            return response()->json([
                'message' => 'You are already unsubscribed.',
            ], 200);
        }

        $subscriber->unsubscribe();

        return response()->json([
            'message' => 'You have been successfully unsubscribed from our newsletter.',
        ], 200);
    }

    /**
     * Get subscription status.
     */
    public function status(Request $request): JsonResponse
    {
        $email = $request->input('email');

        if (! $email) {
            return response()->json([
                'message' => 'Email address is required.',
            ], 422);
        }

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if (! $subscriber) {
            return response()->json([
                'message' => 'Subscriber not found.',
                'subscribed' => false,
            ], 404);
        }

        return response()->json([
            'subscribed' => $subscriber->isActive(),
            'confirmed' => $subscriber->isConfirmed(),
            'status' => $subscriber->status,
            'email' => $subscriber->email,
        ], 200);
    }
}
