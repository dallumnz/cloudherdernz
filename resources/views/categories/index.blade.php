<x-layouts::public>
    <div class="container mx-auto px-4 py-12">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Categories</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">Browse content by category</p>
        </div>

        {{-- Categories Grid --}}
        @if ($categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                @foreach ($categories as $category)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                    <flux:icon name="folder" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $category->posts()->count() }} posts
                                </span>
                            </div>
                            <h2 class="text-xl font-semibold mb-2">
                                <a href="{{ route('categories.show', $category) }}" class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $category->name }}
                                </a>
                            </h2>
                            @if ($category->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                    {{ $category->description }}
                                </p>
                            @endif

                            {{-- Subcategories --}}
                            @if ($category->children->count() > 0)
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-500 mb-2">Subcategories:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($category->children as $child)
                                            <a
                                                href="{{ route('categories.show', $child) }}"
                                                class="text-sm px-2 py-1 rounded bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-blue-900 dark:hover:text-blue-300 transition"
                                            >
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12 max-w-6xl mx-auto">
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl max-w-4xl mx-auto">
                <flux:icon name="folder" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No categories yet</h3>
                <p class="text-gray-500 dark:text-gray-400">Categories will appear here once created.</p>
            </div>
        @endif
    </div>
</x-layouts::public>
