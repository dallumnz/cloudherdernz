<x-public-layout>
    <title>{{ __('Blog Posts') }} | {{ config('app.name') }}</title>

    {{-- Category Filter Buttons --}}
    <section class="container mx-auto px-4 py-6">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('posts.index') }}" 
               class="px-5 py-2 rounded-full border transition-all {{ request()->routeIs('posts.index') && !request('category') ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-indigo-600 dark:hover:border-indigo-500' }}">
                All
            </a>
            @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}" 
               class="px-5 py-2 rounded-full border transition-all bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-indigo-600 dark:hover:border-indigo-500">
                {{ $category->name }}
            </a>
            @endforeach
        </div>
    </section>

    {{-- Posts Grid --}}
    <section class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Posts --}}
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($posts as $post)
                    <a href="{{ route('posts.show', $post) }}" class="group block">
                        <article class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors">
                            <div class="aspect-video bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-800 dark:to-purple-800">
                                @if($post->getFirstMediaUrl('featured'))
                                    <img src="{{ $post->getFirstMediaUrl('featured') }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="p-4">
                                <span class="text-indigo-600 dark:text-indigo-400 text-sm font-medium">
                                    {{ $post->taxonomyTerms->first()?->name ?? 'Post' }}
                                </span>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50 mt-2 mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-2 mb-4">
                                    {{ $post->excerpt ?? Str::limit(strip_tags($post->rendered_html), 100) }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                    <span>{{ $post->author?->name ?? 'Admin' }}</span>
                                    <span>{{ $post->published_at?->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </article>
                    </a>
                    @empty
                    <div class="col-span-2 text-center py-12">
                        <p class="text-slate-500 dark:text-slate-400">No posts found.</p>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-6">
                {{-- Search --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-4">Search</h3>
                    <form action="{{ route('search.results') }}" method="GET" class="space-y-4">
                        <input 
                            type="text" 
                            name="q"
                            placeholder="Search articles..." 
                            class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500"
                        >
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                            Search
                        </button>
                    </form>
                </div>

                {{-- Popular Posts --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-4">Popular Posts</h3>
                    <div class="space-y-4">
                        @foreach($popularTags->take(5) as $tag)
                        <div class="flex gap-3">
                            <span class="text-2xl font-bold text-slate-300 dark:text-slate-600">{{ $loop->index + 1 }}</span>
                            <div>
                                <a href="{{ route('tags.show', $tag->slug) }}" class="font-medium text-slate-900 dark:text-slate-50 hover:text-indigo-600 dark:hover:text-indigo-400 transition line-clamp-2">
                                    {{ $tag->name }}
                                </a>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $tag->posts_count }} posts</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Newsletter --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-2">Stay Updated</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">Subscribe to our newsletter for the latest updates.</p>
                    <form class="flex">
                        <input 
                            type="email" 
                            placeholder="Your email" 
                            class="flex-1 px-4 py-2 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-l-lg text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500"
                        >
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-r-lg transition">
                            Subscribe
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </section>
</x-public-layout>
