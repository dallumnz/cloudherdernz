<div>
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 dark:from-blue-900 dark:to-indigo-900 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to CloudHerder.nz</h1>
                <p class="text-xl text-blue-100 mb-8">Discover amazing content, stories, and insights from our community.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="#featured" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                        Explore Content
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition">
                            Join Community
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    {{-- Featured Posts --}}
    <section id="featured" class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Featured Posts</h2>
                <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    View All →
                </a>
            </div>

            @if ($featuredPosts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($featuredPosts as $post)
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
                                <div class="flex items-center space-x-2 mb-3">
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 px-2 py-1 rounded">
                                        {{ $post->postable?->getKey() ? class_basename($post->postable_type) . ' Post' : 'Post' }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $post->published_at?->format('M d, Y') }}
                                    </span>
                                </div>
                                <h3 class="text-xl font-semibold mb-2">
                                    <a href="{{ route('posts.show', $post) }}" class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-4">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 150) }}
                                </p>
                                <div class="flex items-center justify-between">
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
            @else
                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl">
                    <flux:icon name="document-text" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                    <p class="text-gray-500 dark:text-gray-400">No featured posts yet. Check back soon!</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Categories & Tags --}}
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Categories --}}
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Browse by Category</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($categories as $category)
                            <a
                                href="{{ route('categories.show', $category) }}"
                                class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition group"
                            >
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                        <flux:icon name="folder" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                            {{ $category->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ $category->posts_count }} posts</p>
                                    </div>
                                </div>
                                <flux:icon name="chevron-right" class="w-5 h-5 text-gray-400 group-hover:text-blue-600" />
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Popular Tags --}}
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Popular Tags</h2>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($popularTags as $tag)
                                <a
                                    href="{{ route('tags.show', $tag) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-blue-900 dark:hover:text-blue-300 transition"
                                >
                                    {{ $tag->name }}
                                    <span class="ml-1.5 text-xs text-gray-500">({{ $tag->posts_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Recent Posts Sidebar --}}
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Posts</h3>
                        <div class="space-y-4">
                            @foreach ($recentPosts->take(5) as $post)
                                <a href="{{ route('posts.show', $post) }}" class="block group">
                                    <h4 class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 line-clamp-2">
                                        {{ $post->title }}
                                    </h4>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $post->published_at?->format('M d, Y') }} • {{ $post->author?->name ?? 'Unknown' }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Newsletter / CTA --}}
    <section class="py-12 bg-gray-900 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Stay Updated</h2>
            <p class="text-gray-400 mb-8 max-w-2xl mx-auto">Get the latest posts and updates delivered straight to your inbox. Join our community today!</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto">
                @guest
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                        Create Account
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3 border border-gray-600 hover:border-gray-500 text-white font-semibold rounded-lg transition">
                        Sign In
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                        Go to Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>
</div>
