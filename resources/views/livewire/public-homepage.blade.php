<div class="bg-gray-50 dark:bg-zinc-900 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Left Column: Featured Post --}}
            <div class="w-full lg:w-2/5">
                @if($featuredPosts->first())
                    <article class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                        @if($featuredPosts->first()->getFeaturedImageUrl('medium'))
                            <a href="{{ route('posts.show', $featuredPosts->first()) }}" class="block aspect-square overflow-hidden">
                                <img 
                                    src="{{ $featuredPosts->first()->getFeaturedImageUrl('medium') }}" 
                                    alt="{{ $featuredPosts->first()->title }}"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                >
                            </a>
                        @else
                            <div class="aspect-square bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <flux:icon name="document-text" class="w-24 h-24 text-gray-400" />
                            </div>
                        @endif
                        <div class="p-6">
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/50 dark:text-blue-400 rounded mb-3">
                                {{ $featuredPosts->first()->postable ? class_basename($featuredPosts->first()->postable_type) . ' Post' : 'Featured' }}
                            </span>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                <a href="{{ route('posts.show', $featuredPosts->first()) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $featuredPosts->first()->title }}
                                </a>
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                                {{ $featuredPosts->first()->excerpt ?: Str::limit(strip_tags($featuredPosts->first()->content), 200) }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <flux:avatar :name="$featuredPosts->first()->author?->name" :initials="$featuredPosts->first()->author?->initials()" size="sm" />
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $featuredPosts->first()->author?->name ?? 'Unknown' }}</span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $featuredPosts->first()->published_at?->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </article>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center">
                        <flux:icon name="document-text" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                        <p class="text-gray-500 dark:text-gray-400">No featured posts yet.</p>
                    </div>
                @endif
            </div>

            {{-- Right Column: Post Grid --}}
            <div class="w-full lg:w-3/5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($recentPosts->skip(1)->take(6) as $index => $post)
                        <article class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
                            @if($post->getFeaturedImageUrl('thumbnail'))
                                <a href="{{ route('posts.show', $post) }}" class="block aspect-video overflow-hidden">
                                    <img 
                                        src="{{ $post->getFeaturedImageUrl('thumbnail') }}" 
                                        alt="{{ $post->title }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    >
                                </a>
                            @else
                                <div class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <flux:icon name="document-text" class="w-10 h-10 text-gray-400" />
                                </div>
                            @endif
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400">
                                        {{ $post->postable ? class_basename($post->postable_type) : 'Post' }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $post->published_at?->format('M d, Y') }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold mb-2 line-clamp-2">
                                    <a href="{{ route('posts.show', $post) }}" class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 80) }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1">
                                        <flux:avatar :name="$post->author?->name" :initials="$post->author?->initials()" size="xs" />
                                        <span class="text-xs text-gray-500">{{ $post->author?->name ?? 'Unknown' }}</span>
                                    </div>
                                    <a href="{{ route('posts.show', $post) }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                                        Read →
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- View All Link --}}
                @if($recentPosts->count() > 1)
                    <div class="mt-8 text-left">
                        <a href="{{ route('posts.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                            View All Posts
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
