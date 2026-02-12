<x-layouts::public>
    <article class="container mx-auto px-4 py-12">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
            <span>/</span>
            <a href="{{ route('posts.index') }}" class="hover:text-blue-600">Posts</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white truncate">{{ $post->title }}</span>
        </nav>

        {{-- Post Header --}}
        <header class="max-w-4xl mx-auto mb-8">
            <div class="flex items-center space-x-2 mb-4">
                <span class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 px-3 py-1 rounded-full">
                    {{ $post->postable?->getKey() ? class_basename($post->postable_type) . ' Post' : 'Post' }}
                </span>
                <span class="text-sm text-gray-500">
                    {{ $post->published_at?->format('F d, Y') }}
                </span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                {{ $post->title }}
            </h1>

            {{-- Author --}}
            <div class="flex items-center space-x-4">
                <flux:avatar :name="$post->author?->name" :initials="$post->author?->initials()" />
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $post->author?->name ?? 'Unknown' }}</p>
                    <p class="text-sm text-gray-500">Author</p>
                </div>
            </div>
        </header>

        {{-- Featured Image --}}
        @if ($post->getFeaturedImageUrl('featured'))
            <div class="max-w-5xl mx-auto mb-12">
                <img
                    src="{{ $post->getFeaturedImageUrl('featured') }}"
                    alt="{{ $post->title }}"
                    class="w-full h-auto rounded-xl shadow-lg"
                >
            </div>
        @endif

        {{-- Post Content --}}
        <div class="max-w-4xl mx-auto">
            {{-- Excerpt --}}
            @if ($post->excerpt)
                <p class="text-xl text-gray-600 dark:text-gray-400 italic mb-8 border-l-4 border-blue-500 pl-4">
                    {{ $post->excerpt }}
                </p>
            @endif

            {{-- Main Content --}}
            <div class="prose prose-lg dark:prose-invert max-w-none">
                {!! $post->content !!}
            </div>

            {{-- Gallery --}}
            @php
                $galleryImages = $post->getGalleryUrls('gallery');
            @endphp
            @if (count($galleryImages) > 0)
                <div class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Gallery</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($galleryImages as $imageUrl)
                            <a href="{{ $imageUrl }}" target="_blank" class="block aspect-square overflow-hidden rounded-lg hover:opacity-90 transition">
                                <img src="{{ $imageUrl }}" alt="Gallery image" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tags & Categories --}}
            @if ($post->taxonomyTerms->count() > 0)
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">Tagged:</span>
                        @foreach ($post->taxonomyTerms as $term)
                            <a
                                href="{{ route($term->taxonomy?->type === 'tag' ? 'tags.show' : 'categories.show', $term) }}"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-blue-900 dark:hover:text-blue-300 transition"
                            >
                                {{ $term->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Navigation --}}
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        ← All Posts
                    </a>
                    @auth
                        @can('edit posts')
                            <a href="{{ route('posts.edit', $post) }}" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 font-medium">
                                Edit Post →
                            </a>
                        @endcan
                    @endauth
                </div>
            </div>
        </div>
    </article>
</x-layouts::public>
