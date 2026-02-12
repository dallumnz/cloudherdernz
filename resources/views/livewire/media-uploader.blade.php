<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Media Library</flux:heading>
        <flux:button
            wire:click="$set('files', [])"
            variant="primary"
        >
            Upload New
        </flux:button>
    </div>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}">
            {{ $message }}
        </flux:callout>
    @endif

    {{-- Upload Section --}}
    <flux:card>
        <flux:heading size="md" class="mb-4">Upload Media</flux:heading>
        <form wire:submit="save" class="space-y-4">
            <div>
                <flux:label for="files">Select Files</flux:label>
                <flux:input
                    type="file"
                    id="files"
                    wire:model="files"
                    multiple
                    class="mt-1"
                />
                <flux:error name="files" />
                <flux:error name="files.*" />
                <p class="mt-1 text-sm text-gray-500">
                    You can upload up to 20 files at once. Max 10MB each.
                </p>
            </div>

            @if (count($files) > 0)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700">
                        Selected Files ({{ count($files) }}):
                    </p>
                    <ul class="mt-2 text-sm text-gray-600 space-y-1">
                        @foreach ($files as $file)
                            <li wire:key="file-{{ $loop->index }}">
                                {{ $file->getClientOriginalName() }}
                                ({{ number_format($file->getSize() / 1024, 2) }} KB)
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center gap-4">
                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">Upload Files</span>
                    <span wire:loading wire:target="save">Uploading...</span>
                </flux:button>

                @if (count($files) > 0)
                    <flux:button
                        type="button"
                        variant="ghost"
                        wire:click="$set('files', [])"
                    >
                        Clear Selection
                    </flux:button>
                @endif
            </div>
        </form>
    </flux:card>

    {{-- Search and Filter --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <flux:input
                type="search"
                wire:model.live="search"
                placeholder="Search media..."
            />
        </div>
        <div class="flex gap-2">
            <flux:select wire:model.live="sortField">
                <option value="created_at">Date</option>
                <option value="name">Name</option>
                <option value="size">Size</option>
            </flux:select>
            <flux:select wire:model.live="sortDirection">
                <option value="desc">Descending</option>
                <option value="asc">Ascending</option>
            </flux:select>
        </div>
    </div>

    {{-- Media Grid --}}
    @if ($media->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach ($media as $item)
                <div wire:key="media-{{ $item->id }}" class="group">
                    <flux:card class="h-full">
                        <div class="space-y-2">
                            @if (str_starts_with($item->mime_type, 'image/'))
                                <img
                                    src="{{ $item->getUrl('preview') }}"
                                    alt="{{ $item->name }}"
                                    class="w-full h-24 object-cover rounded"
                                >
                            @else
                                <div class="w-full h-24 bg-gray-100 dark:bg-gray-800 rounded flex items-center justify-center">
                                    <flux:icon name="file" class="w-8 h-8 text-gray-400" />
                                </div>
                            @endif

                            <div class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                {{ $item->name }}
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ number_format($item->size / 1024, 2) }} KB
                            </div>

                            <div class="flex gap-1">
                                <flux:button
                                    wire:click="deleteMedia({{ $item->id }})"
                                    variant="danger"
                                    size="sm"
                                    class="flex-1"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        </div>
                    </flux:card>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $media->links() }}
        </div>
    @else
        <flux:callout variant="info">
            No media found. Upload some files to get started.
        </flux:callout>
    @endif
</div>
