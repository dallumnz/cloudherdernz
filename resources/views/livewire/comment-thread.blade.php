<div class="space-y-6" x-data="{ showReplyForm: null }">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <flux:heading size="lg">
            Comments ({{ $comments->count() }})
        </flux:heading>
        @if ($isAdmin)
            <flux:badge variant="primary" color="purple">
                Moderator View
            </flux:badge>
        @endif
    </div>

    {{-- New Comment Form --}}
    @auth
        <flux:card>
            <form wire:submit="postComment" class="space-y-4">
                <flux:textarea
                    wire:model="newCommentBody"
                    label="Leave a comment"
                    placeholder="Share your thoughts..."
                    rows="3"
                    resize="vertical"
                />
                <div class="flex items-center justify-between">
                    <flux:text variant="secondary" size="sm">
                        Posting as <span class="font-medium">{{ auth()->user()->name }}</span>
                    </flux:text>
                    <flux:button
                        type="submit"
                        variant="primary"
                        icon="paper-airplane"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="postComment">Post Comment</span>
                        <span wire:loading wire:target="postComment">Posting...</span>
                    </flux:button>
                </div>
                @error('newCommentBody')
                    <flux:text variant="danger" size="sm">{{ $message }}</flux:text>
                @enderror
            </form>
        </flux:card>
    @else
        <flux:card class="text-center py-6">
            <flux:text variant="secondary">
                Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">sign in</a> to leave a comment.
            </flux:text>
        </flux:card>
    @endauth

    {{-- Comments List --}}
    <div class="space-y-4">
        @forelse ($comments as $comment)
            <div class="space-y-4">
                {{-- Main Comment --}}
                <flux:card class="relative {{ $comment->is_approved ? '' : 'border-yellow-400 dark:border-yellow-600' }}">
                    {{-- Pending Approval Badge --}}
                    @if (! $comment->is_approved)
                        <div class="absolute -top-2 -right-2">
                            <flux:badge variant="warning" size="sm">Pending Approval</flux:badge>
                        </div>
                    @endif

                    <div class="flex gap-4">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            <flux:avatar
                                :name="$comment->user?->name"
                                :initials="$comment->user?->initials()"
                                size="sm"
                            />
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $comment->user?->name ?? 'Unknown User' }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                {{ $comment->body }}
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-4 mt-3">
                                @auth
                                    <flux:button
                                        wire:click="startReply({{ $comment->id }})"
                                        size="sm"
                                        variant="ghost"
                                        icon="arrow-uturn-left"
                                    >
                                        Reply
                                    </flux:button>
                                @endauth

                                {{-- Admin Controls --}}
                                @if ($isAdmin)
                                    @if (! $comment->is_approved)
                                        <flux:button
                                            wire:click="approveComment({{ $comment->id }})"
                                            size="sm"
                                            variant="ghost"
                                            icon="check"
                                            wire:loading.attr="disabled"
                                        >
                                            <span wire:loading.remove wire:target="approveComment({{ $comment->id }})">Approve</span>
                                            <span wire:loading wire:target="approveComment({{ $comment->id }})">...</span>
                                        </flux:button>
                                    @endif
                                    <flux:button
                                        wire:click="deleteComment({{ $comment->id }})"
                                        wire:confirm="Are you sure you want to delete this comment?"
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                        class="text-red-600 hover:text-red-700"
                                    >
                                        Delete
                                    </flux:button>
                                @elseif (auth()->id() === $comment->user_id)
                                    <flux:button
                                        wire:click="deleteComment({{ $comment->id }})"
                                        wire:confirm="Are you sure you want to delete your comment?"
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                        class="text-red-600 hover:text-red-700"
                                    >
                                        Delete
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Reply Form --}}
                    @if ($replyingTo === $comment->id)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <form wire:submit="postReply" class="space-y-3">
                                <flux:textarea
                                    wire:model="replyBody"
                                    placeholder="Write your reply..."
                                    rows="2"
                                    resize="vertical"
                                />
                                <div class="flex items-center gap-2">
                                    <flux:button
                                        type="submit"
                                        variant="primary"
                                        size="sm"
                                        wire:loading.attr="disabled"
                                    >
                                        <span wire:loading.remove wire:target="postReply">Post Reply</span>
                                        <span wire:loading wire:target="postReply">Posting...</span>
                                    </flux:button>
                                    <flux:button
                                        type="button"
                                        wire:click="cancelReply"
                                        variant="ghost"
                                        size="sm"
                                    >
                                        Cancel
                                    </flux:button>
                                </div>
                                @error('replyBody')
                                    <flux:text variant="danger" size="sm">{{ $message }}</flux:text>
                                @enderror
                            </form>
                        </div>
                    @endif
                </flux:card>

                {{-- Nested Replies --}}
                @if ($comment->children->count() > 0)
                    <div class="ml-8 space-y-3">
                        @foreach ($comment->children as $reply)
                            <flux:card class="relative {{ $reply->is_approved ? '' : 'border-yellow-400 dark:border-yellow-600' }}">
                                {{-- Pending Approval Badge --}}
                                @if (! $reply->is_approved)
                                    <div class="absolute -top-2 -right-2">
                                        <flux:badge variant="warning" size="sm">Pending</flux:badge>
                                    </div>
                                @endif

                                <div class="flex gap-3">
                                    {{-- Avatar --}}
                                    <div class="flex-shrink-0">
                                        <flux:avatar
                                            :name="$reply->user?->name"
                                            :initials="$reply->user?->initials()"
                                            size="xs"
                                        />
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                                {{ $reply->user?->name ?? 'Unknown User' }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ $reply->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <div class="text-gray-700 dark:text-gray-300 text-sm whitespace-pre-wrap">
                                            {{ $reply->body }}
                                        </div>

                                        {{-- Admin Controls for Reply --}}
                                        @if ($isAdmin)
                                            <div class="flex items-center gap-2 mt-2">
                                                @if (! $reply->is_approved)
                                                    <flux:button
                                                        wire:click="approveComment({{ $reply->id }})"
                                                        size="xs"
                                                        variant="ghost"
                                                        icon="check"
                                                    >
                                                        Approve
                                                    </flux:button>
                                                @endif
                                                <flux:button
                                                    wire:click="deleteComment({{ $reply->id }})"
                                                    wire:confirm="Delete this reply?"
                                                    size="xs"
                                                    variant="ghost"
                                                    icon="trash"
                                                    class="text-red-600 hover:text-red-700"
                                                >
                                                    Delete
                                                </flux:button>
                                            </div>
                                        @elseif (auth()->id() === $reply->user_id)
                                            <div class="flex items-center gap-2 mt-2">
                                                <flux:button
                                                    wire:click="deleteComment({{ $reply->id }})"
                                                    wire:confirm="Delete your reply?"
                                                    size="xs"
                                                    variant="ghost"
                                                    icon="trash"
                                                    class="text-red-600 hover:text-red-700"
                                                >
                                                    Delete
                                                </flux:button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </flux:card>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <flux:card class="text-center py-8">
                <flux:icon name="chat-bubble-left-right" class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                <flux:text variant="secondary">
                    No comments yet. Be the first to share your thoughts!
                </flux:text>
            </flux:card>
        @endforelse
    </div>
</div>
