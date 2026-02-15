<x-layouts::public>
    <x-slot:head>
        <meta name="robots" content="noindex, follow">
    </x-slot:head>

    <div class="container mx-auto px-4 py-12">
        {{-- Search Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Search</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">Find posts by title or content</p>

            {{-- Search Form --}}
            <form action="{{ route('search.index') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ $query ?? '' }}"
                        placeholder="Search posts..."
                        class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                        minlength="2"
                        maxlength="255"
                    >
                    <button
                        type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                    >
                        Search
                    </button>
                </div>
            </form>
        </div>

        {{-- Search Results --}}
        @isset($query)
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400">
                    Search results for "<span class="font-semibold text-gray-900 dark:text-white">{{ $query }}</span>"
                    @if ($posts && $posts->total() > 0)
                        <span class="text-sm">({{ $posts->total() }} {{ Str::plural('result', $posts->total()) }})</span>
                    @endif
                </p>
            </div>

            @if ($posts && $posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($posts as $post)
                        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
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
                                <div class="flex items-center space-x-2 mb-3">
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 px-2 py-1 rounded">
                                        {{ $post->postable?->getKey() ? class_basename($post->postable_type) . ' Post' : 'Post' }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $post->published_at?->format('M d, Y') }}
                                    </span>
                                </div>
                                <h2 class="text-xl font-semibold mb-3">
                                    <a href="{{ route('posts.show', $post) }}" class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-4">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 150) }}
                                </p>

                                {{-- Tags --}}
                                @if ($post->taxonomyTerms->count() > 0)
                                    <div class="flex flex-wrap gap-1 mb-4">
                                        @foreach ($post->taxonomyTerms->take(3) as $term)
                                            <a
                                                href="{{ route($term->taxonomy?->type === 'tag' ? 'tags.show' : 'categories.show', $term) }}"
                                                class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-blue-900 dark:hover:text-blue-300 transition"
                                            >
                                                {{ $term->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <flux:avatar :name="$post->author?->name" :initials="$post->author?->initials()" size="sm" />
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $post->author?->name ?? 'Unknown' }}</span>
                                    </div>
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
                {{-- No Results --}}
                <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <flux:icon name="magnifying-glass" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No results found</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">We couldn't find any posts matching "{{ $query }}"</p>
                    <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        Browse all posts →
                    </a>
                </div>
            @endif
        @endisset
    </div>
</x-layouts::public>
