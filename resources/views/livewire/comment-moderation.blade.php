<div class="space-y-6">
    <flux:heading size="xl">Comment Moderation</flux:heading>

    @if (session('message'))
        <flux:callout variant="success">
            {{ session('message') }}
        </flux:callout>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <flux:card class="text-center">
            <div class="text-2xl font-bold">{{ $stats['pending'] }}</div>
            <div class="text-gray-500">Pending</div>
        </flux:card>
        <flux:card class="text-center">
            <div class="text-2xl font-bold">{{ $stats['approved'] }}</div>
            <div class="text-gray-500">Approved</div>
        </flux:card>
        <flux:card class="text-center">
            <div class="text-2xl font-bold">{{ $stats['rejected'] }}</div>
            <div class="text-gray-500">Rejected</div>
        </flux:card>
    </div>

    {{-- Filters & Search --}}
    <flux:card>
        <div class="flex flex-col sm:flex-row gap-4">
            <flux:input
                wire:model="search"
                placeholder="Search comments..."
                icon="magnifying-glass"
                class="sm:w-64"
            />

            <div class="flex gap-2">
                <button wire:click="$set('filter', 'pending')" class="px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                    Pending
                </button>
                <button wire:click="$set('filter', 'approved')" class="px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                    Approved
                </button>
                <button wire:click="$set('filter', 'rejected')" class="px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                    Rejected
                </button>
            </div>
        </div>
    </flux:card>

    {{-- Comments List --}}
    <flux:card>
        @if ($comments->isEmpty())
            <div class="text-center py-12 text-gray-500">
                <flux:icon name="chat-bubble-left-right" class="w-12 h-12 mx-auto mb-4" />
                <p>No comments found.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($comments as $comment)
                    <div class="border rounded-lg p-4 dark:border-gray-700">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold">{{ $comment->user?->name ?? 'Unknown' }}</span>
                                    @if ($comment->trashed())
                                        <flux:badge color="red">Rejected</flux:badge>
                                    @elseif ($comment->is_approved)
                                        <flux:badge color="green">Approved</flux:badge>
                                    @else
                                        <flux:badge color="yellow">Pending</flux:badge>
                                    @endif
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $comment->body }}</p>
                                <div class="text-sm text-gray-500">
                                    On: {{ $comment->commentable?->title ?? 'Unknown Post' }}
                                    • {{ $comment->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if (!$comment->trashed() && !$comment->is_approved)
                                    <button wire:click="approve({{ $comment->id }})" class="px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                        Approve
                                    </button>
                                @endif
                                @if (!$comment->trashed())
                                    <button wire:click="reject({{ $comment->id }})" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                        Reject
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $comments->links() }}
            </div>
        @endif
    </flux:card>
</div>
