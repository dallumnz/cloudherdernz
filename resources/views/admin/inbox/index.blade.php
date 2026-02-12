<x-layouts::app>
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Inbox</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage contact form submissions</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('admin.inbox.index') }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 {{ $status === 'all' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $counts['all'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">All Messages</div>
            </a>
            <a href="{{ route('admin.inbox.index', ['status' => 'unread']) }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 {{ $status === 'unread' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-2xl font-bold text-blue-600">{{ $counts['unread'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Unread</div>
            </a>
            <a href="{{ route('admin.inbox.index', ['status' => 'read']) }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 {{ $status === 'read' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-2xl font-bold text-green-600">{{ $counts['read'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Read</div>
            </a>
            <a href="{{ route('admin.inbox.index', ['status' => 'archived']) }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4 {{ $status === 'archived' ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-2xl font-bold text-gray-600">{{ $counts['archived'] }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Archived</div>
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

        {{-- Contacts List --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
            @if ($contacts->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($contacts as $contact)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition {{ $contact->isUnread() ? 'bg-blue-50/50 dark:bg-blue-900/20' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                            <a href="{{ route('admin.inbox.show', $contact) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $contact->subject ?? '(No Subject)' }}
                                            </a>
                                        </h3>
                                        @if ($contact->isUnread())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                New
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $contact->name }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $contact->email }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $contact->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">
                                        {{ $contact->messagePreview() }}
                                    </p>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    @can('manage contacts')
                                        @if ($contact->isUnread())
                                            <form action="{{ route('admin.inbox.read', $contact) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Mark as read">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.inbox.spam', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to mark this as spam and block the sender?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300" title="Mark as spam">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                    @can('delete contacts')
                                        <form action="{{ route('admin.inbox.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $contacts->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No messages</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        @if ($status === 'unread')
                            No unread messages. Great job!
                        @elseif ($status === 'archived')
                            No archived messages.
                        @else
                            No contact submissions yet.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
