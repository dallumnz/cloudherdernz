<x-layouts::app>
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Subscriber</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Update subscriber information</p>
            </div>
            <a href="{{ route('admin.newsletter-subscribers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-red-700 dark:text-red-300 font-medium">Please fix the following errors:</p>
                        <ul class="mt-1 text-sm text-red-600 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Edit Form --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
            <form action="{{ route('admin.newsletter-subscribers.update', $subscriber) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Email (Read-only) --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                        <input type="email" id="email" value="{{ $subscriber->email }}" disabled
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-zinc-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email cannot be changed.</p>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $subscriber->name) }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select id="status" name="status"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" {{ old('status', $subscriber->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ old('status', $subscriber->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="unsubscribed" {{ old('status', $subscriber->status) === 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                        </select>
                    </div>
                </div>

                {{-- Current Status Info --}}
                <div class="mt-6 p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Status</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Confirmation:</span>
                            @if ($subscriber->isConfirmed())
                                <span class="ml-2 inline-flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Confirmed
                                </span>
                            @else
                                <span class="ml-2 inline-flex items-center text-yellow-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pending
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Subscribed:</span>
                            <span class="ml-2 text-gray-900 dark:text-white">{{ $subscriber->subscribed_at?->format('M d, Y') ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">IP Address:</span>
                            <span class="ml-2 text-gray-900 dark:text-white">{{ $subscriber->ip_address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.newsletter-subscribers.show', $subscriber) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Update Subscriber
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
