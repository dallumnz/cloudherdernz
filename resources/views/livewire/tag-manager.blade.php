<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Tag Management</flux:heading>
        <flux:button wire:click="create" variant="primary">
            Create Tag
        </flux:button>
    </div>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}" wire:poll.5s="$set('message', '')">
            {{ $message }}
        </flux:callout>
    @endif

    {{-- Search --}}
    <div class="flex items-center space-x-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search tags..."
            class="max-w-md"
        />
    </div>

    {{-- Form --}}
    @if ($showForm)
        <flux:card>
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit Tag' : 'Create Tag' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        wire:model="name"
                        label="Name"
                        placeholder="Tag name"
                        required
                    />
                    <flux:input
                        wire:model="slug"
                        label="Slug"
                        placeholder="tag-slug"
                        description="Leave empty to auto-generate from name"
                    />
                </div>

                <flux:textarea
                    wire:model="description"
                    label="Description"
                    placeholder="Optional description"
                    rows="3"
                />

                <div class="flex items-center space-x-3">
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </flux:button>
                    <flux:button type="button" wire:click="cancel" variant="ghost">
                        Cancel
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Tags List --}}
    <flux:card>
        @if ($tags->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4 font-semibold">Name</th>
                            <th class="py-3 px-4 font-semibold">Slug</th>
                            <th class="py-3 px-4 font-semibold">Description</th>
                            <th class="py-3 px-4 font-semibold">Posts</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tags as $tag)
                            <tr class="border-b last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4 font-medium">{{ $tag->name }}</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $tag->slug }}</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                    {{ $tag->description ?: '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $tag->posts()->count() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <flux:button wire:click="edit({{ $tag->id }})" size="sm" variant="ghost">
                                            Edit
                                        </flux:button>
                                        <flux:button
                                            wire:click="delete({{ $tag->id }})"
                                            wire:confirm="Are you sure you want to delete '{{ $tag->name }}'?"
                                            size="sm"
                                            variant="danger"
                                        >
                                            Delete
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tags->links() }}
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <flux:icon name="tag" class="w-12 h-12 mx-auto mb-4" />
                <p>No tags found.</p>
                @if ($search)
                    <p class="text-sm mt-2">Try adjusting your search.</p>
                @endif
            </div>
        @endif
    </flux:card>
</div>
