<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Post Management</flux:heading>
        @can('create posts')
            <button wire:click="create" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Post
            </button>
        @endcan
    </div>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}" wire:poll.5s="$set('message', '')">
            {{ $message }}
        </flux:callout>
    @endif

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                type="search"
                placeholder="Search posts..."
                class="max-w-md"
            />
        </div>
        <div class="flex gap-2">
            <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                <option value="">All Statuses</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
                <option value="archived">Archived</option>
            </flux:select>
            <flux:select wire:model.live="postTypeFilter" placeholder="All Types">
                <option value="">All Types</option>
                @foreach ($this->postTypes as $type)
                    <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Form --}}
    @if ($showForm)
        <flux:card>
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit Post' : 'Create Post' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        wire:model="title"
                        label="Title"
                        placeholder="Post title"
                        required
                    />
                    <flux:input
                        wire:model="slug"
                        label="Slug"
                        placeholder="post-slug"
                        description="Leave empty to auto-generate from title"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="postTypeValue" label="Post Type">
                        @foreach ($this->postTypes as $type)
                            <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="status" label="Status" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </flux:select>
                </div>

                <flux:input
                    wire:model="publishedAt"
                    type="datetime-local"
                    label="Published At"
                    description="When to publish (optional)"
                />

                <flux:textarea
                    wire:model="excerpt"
                    label="Excerpt"
                    placeholder="Short description of the post"
                    rows="3"
                />

                <flux:textarea
                    wire:model="content"
                    label="Content"
                    placeholder="Full post content"
                    rows="10"
                />

                {{-- Tags --}}
                <div>
                    <flux:text variant="secondary" size="sm" class="mb-2">Tags</flux:text>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->tags as $tag)
                            <label class="inline-flex items-center px-3 py-1 rounded-full text-sm cursor-pointer transition-colors
                                {{ in_array($tag->id, $selectedTags) ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                <input
                                    type="checkbox"
                                    wire:model="selectedTags"
                                    value="{{ $tag->id }}"
                                    class="sr-only"
                                >
                                {{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Categories --}}
                <div>
                    <flux:text variant="secondary" size="sm" class="mb-2">Categories</flux:text>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->categories as $category)
                            <label class="inline-flex items-center px-3 py-1 rounded-full text-sm cursor-pointer transition-colors
                                {{ in_array($category->id, $selectedCategories) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                <input
                                    type="checkbox"
                                    wire:model="selectedCategories"
                                    value="{{ $category->id }}"
                                    class="sr-only"
                                >
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center space-x-3 pt-4">
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </flux:button>
                    <flux:button type="button" wire:click="cancel" variant="ghost">
                        Cancel
                    </flux:button>

                    @if ($editingId)
                        <flux:button
                            href="{{ route('posts.featured-image', $editingId) }}"
                            variant="outline"
                            size="sm"
                            class="ml-auto"
                        >
                            Featured Image
                        </flux:button>
                        <flux:button
                            href="{{ route('posts.gallery', $editingId) }}"
                            variant="outline"
                            size="sm"
                        >
                            Gallery
                        </flux:button>
                    @endif
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Posts List --}}
    <flux:card>
        @if ($posts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4 font-semibold">Title</th>
                            <th class="py-3 px-4 font-semibold">Type</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold">Author</th>
                            <th class="py-3 px-4 font-semibold">Tags</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr class="border-b last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4">
                                    <div class="font-medium">{{ $post->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $post->slug }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $post->postable?->getKey() ? class_basename($post->postable_type) . ' Post' : 'Post' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($post->status === 'published')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @elseif ($post->status === 'draft')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Archived
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                    {{ $post->author?->name ?? 'Unknown' }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($post->taxonomyTerms->take(3) as $term)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700">
                                                {{ $term->name }}
                                            </span>
                                        @endforeach
                                        @if ($post->taxonomyTerms->count() > 3)
                                            <span class="text-xs text-gray-500">+{{ $post->taxonomyTerms->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        @can('edit posts')
                                            <flux:button wire:click="edit({{ $post->id }})" size="sm" variant="ghost">
                                                Edit
                                            </flux:button>
                                        @endcan
                                        <flux:button
                                            href="{{ route('posts.show', $post) }}"
                                            size="sm"
                                            variant="ghost"
                                            target="_blank"
                                        >
                                            View
                                        </flux:button>
                                        @can('delete posts')
                                            <flux:button
                                                wire:click="delete({{ $post->id }})"
                                                wire:confirm="Are you sure you want to delete '{{ $post->title }}'?"
                                                size="sm"
                                                variant="danger"
                                            >
                                                Delete
                                            </flux:button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <flux:icon name="document-text" class="w-12 h-12 mx-auto mb-4" />
                <p>No posts found.</p>
                @if ($search || $statusFilter || $postTypeFilter)
                    <p class="text-sm mt-2">Try adjusting your filters.</p>
                @endif
            </div>
        @endif
    </flux:card>
</div>
