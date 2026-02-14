<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin Newsletter Subscriber Controller
 *
 * Handles newsletter subscriber management for administrators.
 */
class NewsletterSubscriberController extends Controller
{
    /**
     * Display a listing of subscribers.
     *
     * Shows subscriber list with filtering by status.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', NewsletterSubscriber::class);

        $status = $request->get('status', 'all');

        $subscribers = NewsletterSubscriber::query()
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest('subscribed_at')
            ->paginate(20);

        $counts = [
            'all' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::active()->count(),
            'pending' => NewsletterSubscriber::pending()->count(),
            'unsubscribed' => NewsletterSubscriber::unsubscribed()->count(),
        ];

        return view('admin.newsletter-subscribers.index', compact('subscribers', 'counts', 'status'));
    }

    /**
     * Display the specified subscriber.
     */
    public function show(NewsletterSubscriber $subscriber): View
    {
        $this->authorize('view', $subscriber);

        return view('admin.newsletter-subscribers.show', compact('subscriber'));
    }

    /**
     * Show the form for editing the specified subscriber.
     */
    public function edit(NewsletterSubscriber $subscriber): View
    {
        $this->authorize('update', $subscriber);

        return view('admin.newsletter-subscribers.edit', compact('subscriber'));
    }

    /**
     * Update the specified subscriber.
     */
    public function update(Request $request, NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->authorize('update', $subscriber);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,pending,unsubscribed'],
            'preferences' => ['nullable', 'array'],
        ]);

        $subscriber->update($validated);

        return redirect()
            ->route('admin.newsletter-subscribers.index')
            ->with('success', 'Subscriber updated successfully.');
    }

    /**
     * Remove the specified subscriber.
     */
    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->authorize('delete', $subscriber);

        $subscriber->delete();

        return redirect()
            ->route('admin.newsletter-subscribers.index')
            ->with('success', 'Subscriber deleted successfully.');
    }

    /**
     * Confirm the subscriber.
     */
    public function confirm(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->authorize('manageStatus', $subscriber);

        $subscriber->confirm();

        return redirect()
            ->back()
            ->with('success', 'Subscriber confirmed successfully.');
    }

    /**
     * Unsubscribe the subscriber.
     */
    public function unsubscribe(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->authorize('manageStatus', $subscriber);

        $subscriber->unsubscribe();

        return redirect()
            ->back()
            ->with('success', 'Subscriber unsubscribed successfully.');
    }

    /**
     * Reactivate the subscriber.
     */
    public function reactivate(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->authorize('manageStatus', $subscriber);

        $subscriber->reactivate();

        return redirect()
            ->back()
            ->with('success', 'Subscriber reactivated successfully.');
    }

    /**
     * Export subscribers to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('export', NewsletterSubscriber::class);

        $status = $request->get('status', 'active');

        $subscribers = NewsletterSubscriber::query()
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->whereNotNull('confirmed_at')
            ->get();

        $filename = 'subscribers_'.now()->format('Y-m-d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email', 'Name', 'Status', 'Subscribed At', 'Confirmed At']);

            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name,
                    $subscriber->status,
                    $subscriber->subscribed_at?->format('Y-m-d H:i:s'),
                    $subscriber->confirmed_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
