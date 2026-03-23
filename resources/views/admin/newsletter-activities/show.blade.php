<x-layouts::app :title="__('Newsletter Activity')">
    <div class="container mx-auto px-4 py-8">
        @php 
            $newsletterPost = $activity->newsletterPost;
            $post = $newsletterPost ? \App\Models\Post::where('postable_type', \App\Models\NewsletterPost::class)->where('postable_id', $newsletterPost->id)->first() : null;
        @endphp
        
        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('admin.newsletter-activities.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Back to Activities
                </a>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $post?->title ?? 'Newsletter' }}</h1>
            
            <div class="flex items-center space-x-4 mb-6">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $activity->isSent() ? 'bg-green-100 text-green-800' : '' }}
                    {{ $activity->isSending() ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $activity->isQueued() ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $activity->isFailed() ? 'bg-red-100 text-red-800' : '' }}
                    {{ $activity->isCancelled() ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $activity->isDraft() ? 'bg-gray-100 text-gray-600' : '' }}">
                    {{ ucfirst($activity->status) }}
                </span>
                <span class="text-sm text-gray-500">
                    Created {{ $activity->created_at->diffForHumans() }}
                </span>
            </div>

            @if ($activity->isSending())
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <div class="flex items-center">
                        <svg class="animate-spin w-5 h-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-blue-700 dark:text-blue-300">Sending newsletter...</p>
                    </div>
                    <div class="mt-3">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $activity->getProgressPercentage() }}%"></div>
                        </div>
                        <p class="text-sm text-blue-600 mt-1">{{ $activity->getProgressPercentage() }}% complete</p>
                    </div>
                </div>
            @endif

            @if ($activity->error_message)
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-red-700 dark:text-red-300 font-medium">Error:</p>
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $activity->error_message }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistics</h2>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Recipients</dt>
                        <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activity->recipients_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Sent</dt>
                        <dd class="text-2xl font-bold text-green-600">{{ $activity->sent_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Failed</dt>
                        <dd class="text-2xl font-bold text-red-600">{{ $activity->failed_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Type</dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $activity->is_test ? 'Test' : 'Production' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Timeline</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900 dark:text-white">{{ $activity->created_at->format('M j, Y H:i:s') }}</span>
                    </div>
                    @if ($activity->started_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Started</span>
                            <span class="text-gray-900 dark:text-white">{{ $activity->started_at->format('M j, Y H:i:s') }}</span>
                        </div>
                    @endif
                    @if ($activity->sent_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Completed</span>
                            <span class="text-gray-900 dark:text-white">{{ $activity->sent_at->format('M j, Y H:i:s') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex space-x-3">
                @if ($activity->canBeRetried())
                    <form action="{{ route('admin.newsletter-activities.retry', $activity) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Retry Send
                        </button>
                    </form>
                @endif

                @if ($activity->canBeCancelled())
                    <form action="{{ route('admin.newsletter-activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('Cancel this newsletter?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Cancel
                        </button>
                    </form>
                @endif

                @if ($activity->isSent())
                    <a href="{{ route('newsletter.show', ['uuid' => $activity->newsletterPost->id]) }}" 
                       target="_blank"
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        View Web Version
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app>
