<x-layouts::public>
    <div class="container mx-auto px-4 py-12">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
            <span>/</span>
            <a href="{{ route('categories.index') }}" class="hover:text-blue-600">Categories</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white">{{ $category->name }}</span>
        </nav>

        {{-- Category Header --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center p-4 bg-indigo-100 dark:bg-indigo-900 rounded-full mb-4">
                <flux:icon name="folder" class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ $category->description }}</p>
            @endif
            <p class="text-sm text-gray-500 mt-4">
                {{ $category->posts()->count() }} post{{ $category->posts()->count() !== 1 ? 's' : '' }}
            </p>
        </div>

        {{-- Parent Category Link --}}
        @if ($category->parent)
            <div class="text-center mb-8">
                <a href="{{ route('categories.show', $category->parent) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <flux:icon name="arrow-left" class="w-4 h-4 mr-2" />
                    Back to {{ $category->parent->name }}
                </a>
            </div>
        @endif

        {{-- Subcategories --}}
        @if ($category->children->count() > 0)
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Subcategories</h2>
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach ($category->children as $child)
                        <a
                            href="{{ route('categories.show', $child) }}"
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-indigo-900 dark:hover:text-indigo-300 transition"
                        >
                            <flux:icon name="folder" class="w-4 h-4 mr-2" />
                            {{ $child->name }}
                            <span class="ml-2 text-xs text-gray-500">({{ $child->posts()->count() }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Posts Grid --}}
        @php
            $posts = $category->posts()->published()->latest('published_at')->paginate(12);
        @endphp

        @if ($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($posts as $post)
                    <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        @if ($post->getFeaturedImageUrl('thumbnail'))
                            <a href="{{ route('posts.show', $post) }}" class="block aspect-video overflow-hidden">
                                <img
                                    src="{{ $post->getFeaturedImageUrl('thumbnail') }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                >
                            </a>
                        @else
                            <div class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <flux:icon name="document-text" class="w-12 h-12 text-gray-400" />
                            </div>
                        @endif
                        <div class="p-6">
                            <h2 class="text-xl font-semibold mb-2">
                                <a href="{{ route('posts.show', $post) }}" class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3">
                                {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 150) }}
                            </p>
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <span class="text-sm text-gray-500">
                                    {{ $post->published_at?->format('M d, Y') }}
                                </span>
                                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <flux:icon name="document-text" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No posts yet</h3>
                <p class="text-gray-500 dark:text-gray-400">No posts in "{{ $category->name }}" yet.</p>
            </div>
        @endif
    </div>
</x-layouts::public>
