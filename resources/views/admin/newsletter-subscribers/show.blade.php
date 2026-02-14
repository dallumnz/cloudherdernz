<x-layouts::app>
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Subscriber Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View subscriber information</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.newsletter-subscribers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
                @can('update', $subscriber)
                    <a href="{{ route('admin.newsletter-subscribers.edit', $subscriber) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                @endcan
            </div>
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

        {{-- Subscriber Details --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Basic Information --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $subscriber->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $subscriber->name ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
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
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Confirmation Status</dt>
                                <dd class="mt-1">
                                    @if ($subscriber->isConfirmed())
                                        <span class="inline-flex items-center text-green-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Confirmed on {{ $subscriber->confirmed_at->format('M d, Y H:i') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-yellow-600">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Pending Confirmation
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Subscription Details --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Subscription Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subscribed At</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $subscriber->subscribed_at?->format('M d, Y H:i') ?? 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unsubscribed At</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $subscriber->unsubscribed_at?->format('M d, Y H:i') ?? 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $subscriber->ip_address ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $subscriber->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Preferences --}}
                @if ($subscriber->preferences)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Preferences</h3>
                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                            <pre class="text-sm text-gray-800 dark:text-gray-200 overflow-x-auto">{{ json_encode($subscriber->preferences, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                @can('manageStatus', $subscriber)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
                        <div class="flex flex-wrap gap-3">
                            @if (! $subscriber->isConfirmed())
                                <form action="{{ route('admin.newsletter-subscribers.confirm', $subscriber) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Confirm Subscription
                                    </button>
                                </form>
                            @endif
                            @if ($subscriber->isActive())
                                <form action="{{ route('admin.newsletter-subscribers.unsubscribe', $subscriber) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to unsubscribe this user?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        Unsubscribe
                                    </button>
                                </form>
                            @elseif ($subscriber->isUnsubscribed())
                                <form action="{{ route('admin.newsletter-subscribers.reactivate', $subscriber) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Reactivate
                                    </button>
                                </form>
                            @endif
                            @can('delete', $subscriber)
                                <form action="{{ route('admin.newsletter-subscribers.destroy', $subscriber) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this subscriber? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-layouts::app>
