<x-layouts::app>
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Newsletter Subscribers</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage newsletter subscribers</p>
            </div>
            <div class="flex space-x-3">
                @can('export', App\Models\NewsletterSubscriber::class)
                    <a href="{{ route('admin.newsletter-subscribers.export', ['status' => $status]) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export CSV
                    </a>
                @endcan
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('admin.newsletter-subscribers.index') }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 {{ $status === 'all' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $counts['all'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">All Subscribers</div>
            </a>
            <a href="{{ route('admin.newsletter-subscribers.index', ['status' => 'active']) }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 {{ $status === 'active' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-2xl font-bold text-green-600">{{ $counts['active'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Active</div>
            </a>
            <a href="{{ route('admin.newsletter-subscribers.index', ['status' => 'pending']) }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 {{ $status === 'pending' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-2xl font-bold text-yellow-600">{{ $counts['pending'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Pending</div>
            </a>
            <a href="{{ route('admin.newsletter-subscribers.index', ['status' => 'unsubscribed']) }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 {{ $status === 'unsubscribed' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-2xl font-bold text-gray-600">{{ $counts['unsubscribed'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Unsubscribed</div>
            </a>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Subscribers List --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
            @if ($subscribers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subscriber</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Confirmed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subscribed</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($subscribers as $subscriber)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $subscriber->displayName() }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $subscriber->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($subscriber->isActive())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Active
                                            </span>
                                        @elseif ($subscriber->isPending())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Unsubscribed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($subscriber->isConfirmed())
                                            <span class="inline-flex items-center text-green-600">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Yes
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-yellow-600">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $subscriber->subscribed_at?->diffForHumans() ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.newsletter-subscribers.show', $subscriber) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @can('update', $subscriber)
                                                <a href="{{ route('admin.newsletter-subscribers.edit', $subscriber) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                            @endcan
                                            @can('manageStatus', $subscriber)
                                                @if (! $subscriber->isConfirmed())
                                                    <form action="{{ route('admin.newsletter-subscribers.confirm', $subscriber) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" title="Confirm">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if ($subscriber->isActive())
                                                    <form action="{{ route('admin.newsletter-subscribers.unsubscribe', $subscriber) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to unsubscribe this user?');">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="Unsubscribe">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @elseif ($subscriber->isUnsubscribed())
                                                    <form action="{{ route('admin.newsletter-subscribers.reactivate', $subscriber) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Reactivate">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                            @can('delete', $subscriber)
                                                <form action="{{ route('admin.newsletter-subscribers.destroy', $subscriber) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this subscriber?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $subscribers->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No subscribers</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        @if ($status === 'active')
                            No active subscribers yet.
                        @elseif ($status === 'pending')
                            No pending confirmations.
                        @elseif ($status === 'unsubscribed')
                            No unsubscribed users.
                        @else
                            No newsletter subscribers yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
