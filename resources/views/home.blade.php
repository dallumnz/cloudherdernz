<x-public-layout>
    {{-- Hero / Featured Section --}}
    <section class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Large Featured Post --}}
            @if($featuredPosts->first())
            <a href="{{ route('posts.show', $featuredPosts->first()) }}" class="block group">
            <div class="relative group">
                <div class="w-full h-[400px] lg:h-[600px] bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-600 dark:from-blue-900 dark:via-purple-900 dark:to-indigo-900 rounded-xl overflow-hidden transition-all">
                    @if($featuredPosts->first()->getFirstMediaUrl('featured'))
                        <img src="{{ $featuredPosts->first()->getFirstMediaUrl('featured') }}" alt="{{ $featuredPosts->first()->title }}" class="w-full h-full object-cover">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <span class="inline-block bg-indigo-600 dark:bg-indigo-500 text-white text-xs px-3 py-1 rounded-full mb-3">
                            {{ $featuredPosts->first()->taxonomyTerms->first()?->name ?? 'Featured' }}
                        </span>
                        <h2 class="text-2xl lg:text-3xl font-bold text-white mb-2">
                            {{ $featuredPosts->first()->title }}
                        </h2>
                        <p class="text-slate-300 dark:text-slate-400 text-sm">
                            By {{ $featuredPosts->first()->author?->name ?? 'Admin' }} • {{ $featuredPosts->first()->published_at?->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
            </a>
            @endif
            
            {{-- Smaller Featured Posts Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 lg:grid-rows-3 gap-4">
                @foreach($featuredPosts->skip(1)->take(3) as $post)
                <a href="{{ route('posts.show', $post) }}" class="block group">
                <div class="relative group cursor-pointer h-[185px]">
                    <div class="w-full h-full bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 dark:from-emerald-800 dark:via-teal-800 dark:to-cyan-800 rounded-xl transition-all">
                        @if($post->getFirstMediaUrl('featured'))
                            <img src="{{ $post->getFirstMediaUrl('featured') }}" alt="{{ $post->title }}" class="w-full h-full object-cover rounded-xl">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <span class="inline-block bg-emerald-600 dark:bg-emerald-600 text-white text-xs px-2 py-1 rounded-full mb-2">
                                {{ $post->taxonomyTerms->first()?->name ?? 'Post' }}
                            </span>
                            <h3 class="text-lg font-semibold text-white line-clamp-2">{{ $post->title }}</h3>
                        </div>
                    </div>
                </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Trending Topics --}}
    <section class="container mx-auto px-4 py-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-50 mb-4">
            Trending Topics
        </h2>
        <div class="flex flex-wrap gap-3">
            @foreach($popularTags as $tag)
            <a href="{{ route('tags.show', $tag->slug) }}" 
               class="bg-white dark:bg-slate-800 hover:bg-indigo-600 dark:hover:bg-indigo-500 text-slate-600 dark:text-slate-400 hover:text-white px-5 py-2 rounded-full border border-slate-200 dark:border-slate-700 hover:border-indigo-600 dark:hover:border-indigo-500 transition-all">
                {{ $tag->name }}
            </a>
            @endforeach
        </div>
    </section>

    {{-- Latest Posts --}}
    <section class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-50 mb-6">Latest Posts</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Posts Grid --}}
            <div class="lg:col-span-2 space-y-6">
                @foreach($recentPosts as $post)
                <a href="{{ route('posts.show', $post) }}" class="block group w-full">
                <article class="flex flex-col md:flex-row gap-4 bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-500 transition-colors">
                    <div class="w-full md:w-72 h-48 md:h-auto flex-shrink-0 bg-gradient-to-br from-blue-500 to-purple-600 rounded-t-xl md:rounded-l-xl md:rounded-tr-none">
                        @if($post->getFirstMediaUrl('featured'))
                            <img src="{{ $post->getFirstMediaUrl('featured') }}" alt="{{ $post->title }}" class="w-full h-full object-cover rounded-t-xl md:rounded-l-xl md:rounded-tr-none">
                        @endif
                    </div>
                    <div class="p-4 flex flex-col justify-center">
                        <span class="text-indigo-600 dark:text-indigo-400 text-sm font-medium">
                            {{ $post->taxonomyTerms->first()?->name ?? 'Post' }}
                        </span>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-slate-50 mt-1 mb-2">
                            {{ $post->title }}
                        </h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-2">
                            {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150) }}
                        </p>
                        <div class="flex items-center justify-between text-sm text-slate-500 dark:text-slate-400">
                            <span>By {{ $post->author?->name ?? 'Admin' }}</span>
                            <span>{{ $post->published_at?->format('M d, Y') }}</span>
                        </div>
                    </div>
                </article>
                </a>
                @endforeach
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-8">
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
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                                Search
                            </button>
                        </div>
                    </form>
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex flex-wrap gap-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Popular:</span>
                            @foreach($popularTags->take(5) as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
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
