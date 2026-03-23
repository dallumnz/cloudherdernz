<x-layouts::app :title="__('Newsletter Activities')">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Newsletter Activities</h1>
            <a href="{{ route('admin.newsletter-activities.create') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Newsletter
            </a>
        </div>
        
        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
            @if ($activities->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Newsletter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Recipients</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($activities as $activity)
                            @php 
                                $newsletterPost = $activity->newsletterPost;
                                $post = $newsletterPost ? \App\Models\Post::where('postable_type', \App\Models\NewsletterPost::class)->where('postable_id', $newsletterPost->id)->first() : null;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $post?->title ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $activity->isSent() ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $activity->isSending() ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $activity->isQueued() ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $activity->isFailed() ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $activity->isCancelled() ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $activity->isDraft() ? 'bg-gray-100 text-gray-600' : '' }}">
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $activity->sent_count }} / {{ $activity->recipients_count }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $activity->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.newsletter-activities.show', $activity) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $activities->links() }}
                </div>
            @else
                <div class="p-12 text-center text-gray-500">
                    <p>No newsletter activities yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
