<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Page Management</flux:heading>
        <flux:button wire:click="create" variant="primary">
            Create Page
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
            placeholder="Search pages..."
            class="max-w-md"
        />
    </div>

    {{-- Form --}}
    @if ($showForm)
        <flux:card>
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit Page' : 'Create Page' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        wire:model="title"
                        label="Title"
                        placeholder="Page title"
                        required
                    />
                    <flux:input
                        wire:model="slug"
                        label="Slug"
                        placeholder="page-slug"
                        description="URL: /page/{slug}"
                        required
                    />
                </div>

                <flux:textarea
                    wire:model="content"
                    label="Content"
                    placeholder="Page content (optional)"
                    rows="6"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field label="Status">
                        <flux:select wire:model="status" placeholder="Choose status">
                            <flux:select.option value="draft">Draft</flux:select.option>
                            <flux:select.option value="published">Published</flux:select.option>
                        </flux:select>
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        wire:model="meta_title"
                        label="Meta Title"
                        placeholder="SEO title (optional)"
                    />
                    <flux:input
                        wire:model="meta_description"
                        label="Meta Description"
                        placeholder="SEO description (optional)"
                    />
                </div>

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

    {{-- Pages List --}}
    <flux:card>
        @if ($pages->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4 font-semibold">Title</th>
                            <th class="py-3 px-4 font-semibold">Slug</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold">Created</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $page->title }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="text-blue-600 hover:underline">
                                        /page/{{ $page->slug }}
                                    </a>
                                </td>
                                <td class="py-3 px-4">
                                    @switch($page->status)
                                        @case('published')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Published</span>
                                            @break
                                        @case('draft')
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $page->created_at?->format('M j, Y') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <flux:button size="sm" wire:click="edit({{ $page->id }})" variant="ghost">
                                        Edit
                                    </flux:button>
                                    <flux:button size="sm" wire:click="delete({{ $page->id }})" variant="ghost" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pages->links() }}
            </div>
        @else
            <flux:heading size="lg" class="text-center py-8 text-gray-500">
                No pages found.
            </flux:heading>
        @endif
    </flux:card>
</div>
