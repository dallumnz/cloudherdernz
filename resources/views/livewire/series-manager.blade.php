<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Series Management</flux:heading>
        <flux:button wire:click="create" variant="primary">
            Create Series
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
            placeholder="Search series..."
            class="max-w-md"
        />
    </div>

    {{-- Form --}}
    @if ($showForm)
        <flux:card>
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit Series' : 'Create Series' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        wire:model="title"
                        label="Title"
                        placeholder="Series title"
                        required
                    />
                    <flux:input
                        wire:model="slug"
                        label="Slug"
                        placeholder="series-slug"
                        description="URL: /series/{slug}"
                        required
                    />
                </div>

                <flux:input
                    wire:model="tagline"
                    label="Tagline"
                    placeholder="Short tagline for the series"
                />

                {{-- Markdown Editor --}}
                <div class="space-y-2">
                    <flux:text variant="secondary" size="sm">Description</flux:text>
                    <livewire-markdown-editor wire:model="description" placeholder="Write the series introduction in Markdown..." />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field label="Status">
                        <flux:select wire:model="status" placeholder="Choose status">
                            <flux:select.option value="draft">Draft</flux:select.option>
                            <flux:select.option value="published">Published</flux:select.option>
                        </flux:select>
                    </flux:field>

                    <flux:field label="Series Tag">
                        <flux:select wire:model="taxonomyTermId" placeholder="Select a tag">
                            <option value="">None</option>
                            @foreach ($this->seriesTags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </flux:select>
                        <p class="mt-1 text-xs text-zinc-500">Posts tagged with this will appear on the series page.</p>
                    </flux:field>
                </div>

                {{-- Featured Image --}}
                <div>
                    <flux:label for="featuredImage">Featured Image</flux:label>
                    <flux:input type="file" id="featuredImage" wire:model="featuredImage" accept="image/*" class="mt-1" />
                    <flux:error name="featuredImage" />
                    <p class="mt-1 text-sm text-zinc-500">JPEG, PNG, WebP, AVIF. Optional.</p>

                    @if($editingId && $series && $series->getFirstMediaUrl('featured'))
                        <div class="mt-4">
                            <p class="text-sm font-medium">Current image:</p>
                            <img src="{{ $series->getFirstMediaUrl('featured') }}" alt="Featured" class="mt-2 h-32 rounded">
                        </div>
                    @endif
                </div>

                {{-- SEO Meta Box --}}
                @if($editingId && $series)
                    <livewire:seo-meta-box :seo-data="$series->seo->getAttributes() ?? []" />
                @endif

                <div class="flex items-center space-x-3 pt-4">
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

    {{-- Series List --}}
    <flux:card>
        @if ($seriesList->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4 font-semibold">Title</th>
                            <th class="py-3 px-4 font-semibold">Slug</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold">Tag</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($seriesList as $seriesItem)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4">{{ $seriesItem->title }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('series.show', $seriesItem->slug) }}" target="_blank" class="text-blue-600 hover:underline">
                                        /series/{{ $seriesItem->slug }}
                                    </a>
                                </td>
                                <td class="py-3 px-4">
                                    @switch($seriesItem->status)
                                        @case('published')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Published</span>
                                            @break
                                        @case('draft')
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="py-3 px-4">
                                    {{ $seriesItem->taxonomyTerm?->name ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <flux:button size="sm" wire:click="edit({{ $seriesItem->id }})" variant="ghost">
                                        Edit
                                    </flux:button>
                                    <flux:button size="sm" wire:click="delete({{ $seriesItem->id }})" variant="ghost" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $seriesList->links() }}
            </div>
        @else
            <flux:heading size="lg" class="text-center py-8 text-gray-500">
                No series found.
            </flux:heading>
        @endif
    </flux:card>
</div>
