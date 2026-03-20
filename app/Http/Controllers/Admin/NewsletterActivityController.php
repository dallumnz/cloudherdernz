<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterJob;
use App\Models\NewsletterActivity;
use App\Models\NewsletterPost;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterActivityController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', NewsletterActivity::class);

        $status = $request->get('status', 'all');

        $activities = NewsletterActivity::with(['newsletterPost.posts', 'creator'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $counts = [
            'all' => NewsletterActivity::count(),
            'queued' => NewsletterActivity::queued()->count(),
            'sending' => NewsletterActivity::sending()->count(),
            'sent' => NewsletterActivity::sent()->count(),
            'failed' => NewsletterActivity::failed()->count(),
            'cancelled' => NewsletterActivity::cancelled()->count(),
        ];

        return view('admin.newsletter-activities.index', compact('activities', 'counts', 'status'));
    }

    public function create(): View
    {
        $this->authorize('create', NewsletterActivity::class);

        // Get published newsletter posts that haven't been sent yet
        $availablePosts = NewsletterPost::with(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
            ->whereHas('posts', function ($query) {
                $query->where('status', 'published');
            })
            ->where('is_sent', false)
            ->get();

        $confirmedSubscriberCount = NewsletterSubscriber::confirmed()->active()->count();

        return view('admin.newsletter-activities.create', compact('availablePosts', 'confirmedSubscriberCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', NewsletterActivity::class);

        $validated = $request->validate([
            'newsletter_post_id' => ['required', 'exists:newsletter_posts,id'],
            'is_test' => ['boolean'],
            'test_email' => ['nullable', 'email', 'required_if:is_test,true'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ]);

        $newsletterPost = NewsletterPost::with('posts')->findOrFail($validated['newsletter_post_id']);
        $post = $newsletterPost->posts()->first();

        if (! $post) {
            return redirect()->back()->with('error', 'Newsletter post not found.');
        }

        $activityData = [
            'newsletter_post_id' => $newsletterPost->id,
            'created_by' => auth()->id(),
            'status' => $validated['scheduled_at'] ? 'draft' : 'queued',
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'is_test' => $validated['is_test'] ?? false,
        ];

        if ($activityData['is_test'] && $validated['test_email']) {
            $activityData['test_recipients'] = [$validated['test_email']];
        }

        $activity = NewsletterActivity::create($activityData);

        // If scheduled for later, don't dispatch yet
        if ($validated['scheduled_at']) {
            return redirect()
                ->route('admin.newsletter-activities.index')
                ->with('success', "Newsletter scheduled for {$validated['scheduled_at']}.");
        }

        // Dispatch the job
        SendNewsletterJob::dispatch($activity->id);

        $message = $activityData['is_test']
            ? 'Test newsletter sent to '.$validated['test_email'].'.'
            : 'Newsletter queued for sending.';

        return redirect()
            ->route('admin.newsletter-activities.index')
            ->with('success', $message);
    }

    public function show(NewsletterActivity $activity): View
    {
        $this->authorize('view', $activity);

        $activity->load(['newsletterPost.posts', 'creator']);

        return view('admin.newsletter-activities.show', compact('activity'));
    }

    public function destroy(NewsletterActivity $activity): RedirectResponse
    {
        $this->authorize('delete', $activity);

        if (! $activity->canBeCancelled()) {
            return redirect()->back()->with('error', 'Cannot cancel newsletter that has already started sending.');
        }

        $activity->markAsCancelled();

        return redirect()
            ->route('admin.newsletter-activities.index')
            ->with('success', 'Newsletter sending cancelled.');
    }

    public function retry(NewsletterActivity $activity): RedirectResponse
    {
        $this->authorize('update', $activity);

        if (! $activity->canBeRetried()) {
            return redirect()->back()->with('error', 'Only failed newsletters can be retried.');
        }

        // Reset status and counts
        $activity->update([
            'status' => 'queued',
            'sent_count' => 0,
            'failed_count' => 0,
            'error_message' => null,
            'started_at' => null,
            'sent_at' => null,
        ]);

        SendNewsletterJob::dispatch($activity->id);

        return redirect()
            ->route('admin.newsletter-activities.show', $activity)
            ->with('success', 'Newsletter queued for retry.');
    }

    public function sendTest(Request $request, NewsletterActivity $activity): RedirectResponse
    {
        $this->authorize('update', $activity);

        $validated = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $activity->update([
            'is_test' => true,
            'test_recipients' => [$validated['test_email']],
            'status' => 'queued',
        ]);

        SendNewsletterJob::dispatch($activity->id);

        return redirect()
            ->route('admin.newsletter-activities.show', $activity)
            ->with('success', 'Test email sent to '.$validated['test_email'].'.');
    }
}
