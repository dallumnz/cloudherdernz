<x-layouts::public>
    <div class="container mx-auto px-4 py-12">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
            <span>/</span>
            <a href="{{ route('tags.index') }}" class="hover:text-blue-600">Tags</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white">{{ $tag->name }}</span>
        </nav>

        {{-- Tag Header --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center p-4 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                <flux:icon name="tag" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $tag->name }}</h1>
            @if ($tag->description)
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ $tag->description }}</p>
            @endif
            <p class="text-sm text-gray-500 mt-4">
                {{ $tag->posts()->count() }} post{{ $tag->posts()->count() !== 1 ? 's' : '' }}
            </p>
        </div>

        {{-- Posts Grid --}}
        @php
            $posts = $tag->posts()->published()->latest('published_at')->paginate(12);
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
                <p class="text-gray-500 dark:text-gray-400">No posts are tagged with "{{ $tag->name }}" yet.</p>
            </div>
        @endif
    </div>
</x-layouts::public>
