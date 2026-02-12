<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Category Management</flux:heading>
        <flux:button wire:click="create" variant="primary">
            Create Category
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
            placeholder="Search categories..."
            class="max-w-md"
        />
    </div>

    {{-- Form --}}
    @if ($showForm)
        <flux:card>
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit Category' : 'Create Category' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        wire:model="name"
                        label="Name"
                        placeholder="Category name"
                        required
                    />
                    <flux:input
                        wire:model="slug"
                        label="Slug"
                        placeholder="category-slug"
                        description="Leave empty to auto-generate from name"
                    />
                </div>

                <flux:select wire:model="parentId" label="Parent Category">
                    <option value="">None (Root Category)</option>
                    @foreach ($this->parentCategories as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </flux:select>

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

    {{-- Categories List --}}
    <flux:card>
        @if ($categories->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4 font-semibold">Name</th>
                            <th class="py-3 px-4 font-semibold">Slug</th>
                            <th class="py-3 px-4 font-semibold">Parent</th>
                            <th class="py-3 px-4 font-semibold">Children</th>
                            <th class="py-3 px-4 font-semibold">Posts</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="border-b last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4 font-medium">{{ $category->name }}</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $category->slug }}</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                    {{ $category->parent?->name ?: '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if ($category->children->count() > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $category->children->count() }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $category->posts()->count() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <flux:button wire:click="edit({{ $category->id }})" size="sm" variant="ghost">
                                            Edit
                                        </flux:button>
                                        <flux:button
                                            wire:click="delete({{ $category->id }})"
                                            wire:confirm="Are you sure you want to delete '{{ $category->name }}'? Children will be reassigned to the parent."
                                            size="sm"
                                            variant="danger"
                                        >
                                            Delete
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                            {{-- Show children rows --}}
                            @foreach ($category->children as $child)
                                <tr class="border-b last:border-b-0 bg-gray-50 dark:bg-gray-900/50">
                                    <td class="py-3 px-4 pl-8 font-medium text-gray-600">
                                        ↳ {{ $child->name }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-500">{{ $child->slug }}</td>
                                    <td class="py-3 px-4 text-gray-500">{{ $category->name }}</td>
                                    <td class="py-3 px-4 text-gray-400">-</td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $child->posts()->count() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <flux:button wire:click="edit({{ $child->id }})" size="sm" variant="ghost">
                                                Edit
                                            </flux:button>
                                            <flux:button
                                                wire:click="delete({{ $child->id }})"
                                                wire:confirm="Are you sure you want to delete '{{ $child->name }}'?"
                                                size="sm"
                                                variant="danger"
                                            >
                                                Delete
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <flux:icon name="folder" class="w-12 h-12 mx-auto mb-4" />
                <p>No categories found.</p>
                @if ($search)
                    <p class="text-sm mt-2">Try adjusting your search.</p>
                @endif
            </div>
        @endif
    </flux:card>
</div>
