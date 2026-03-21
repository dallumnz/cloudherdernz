<x-public-layout>
    <x-slot:head>
        <meta name="robots" content="noindex, follow">
    </x-slot:head>

    {{-- Search Header --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-headline font-bold text-on-surface tracking-tight mb-6 letterpress">
                Search Results
            </h1>
            
            @isset($query)
            <p class="text-xl font-headline italic text-on-surface-variant mb-8">
                Found {{ $posts->total() }} {{ Str::plural('result', $posts->total()) }} for "<span class="text-primary font-bold">{{ $query }}</span>"
            </p>
            @else
            <p class="text-xl font-headline italic text-on-surface-variant mb-8">
                Find posts by title or content
            </p>
            @endisset

            {{-- Search Form --}}
            <form action="{{ route('search.results') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="flex gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ $query ?? '' }}"
                        placeholder="Search posts..."
                        class="flex-1 px-6 py-4 bg-surface-container-low border-none rounded-lg text-on-surface font-body placeholder:text-outline/60 focus:ring-2 focus:ring-primary/30 transition-all"
                        required
                        minlength="2"
                        maxlength="255"
                    >
                    <button
                        type="submit"
                        class="px-8 py-4 bg-gradient-to-br from-primary to-primary-container text-on-primary font-bold rounded-lg hover:opacity-90 transition-all flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Search Results --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-12 border-t border-outline-variant/20">
        @isset($query)
            @if ($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($posts as $post)
                <article class="bg-surface-container-low rounded-lg overflow-hidden transition-surface hover:bg-surface-container-high group cursor-pointer">
                    <a href="{{ route('posts.show', $post) }}" class="block">
                        @if ($post->getFirstMediaUrl('featured'))
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ $post->getFirstMediaUrl('featured') }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </div>
                        @else
                        <div class="aspect-video bg-gradient-to-br from-primary to-primary-container flex items-center justify-center">
                            <svg class="w-12 h-12 text-on-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        @endif
                        <div class="p-6">
                            {{-- Post Type Badge --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span class="bg-primary-fixed/50 text-primary text-xs font-label uppercase tracking-wider px-2 py-1 rounded">
                                    {{ $post->taxonomyTerms->first()?->name ?? 'Post' }}
                                </span>
                                <span class="text-xs text-outline">
                                    {{ $post->published_at?->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <h2 class="text-xl font-headline font-bold text-on-surface group-hover:text-primary transition-colors mb-3 leading-tight">
                                {{ $post->title }}
                            </h2>
                            <p class="text-on-surface-variant font-body text-sm line-clamp-3 mb-4">
                                {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 150) }}
                            </p>

                            {{-- Tags --}}
                            @if ($post->taxonomyTerms->count() > 1)
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach ($post->taxonomyTerms->skip(1)->take(3) as $term)
                                <span class="text-xs px-2 py-1 rounded bg-surface-container-high text-on-surface-variant">
                                    {{ $term->name }}
                                </span>
                                @endforeach
                            </div>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t border-outline-variant/20">
                                <span class="text-sm text-on-surface-variant">By {{ $post->author?->name ?? 'Unknown' }}</span>
                                <span class="text-primary text-sm font-medium group-hover:underline">Read More →</span>
                            </div>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
            @else
            {{-- No Results --}}
            <div class="text-center py-20 bg-surface-container-low rounded-xl max-w-2xl mx-auto">
                <svg class="w-16 h-16 mx-auto mb-6 text-outline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-2xl font-headline font-bold text-on-surface mb-3">No results found</h3>
                <p class="text-on-surface-variant font-body mb-8">We couldn't find any posts matching "{{ $query }}"</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('search.index') }}" class="inline-flex items-center justify-center gap-2 text-primary font-label text-sm uppercase tracking-widest hover:underline">
                        Try a new search
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('posts.index') }}" class="inline-flex items-center justify-center gap-2 text-on-surface-variant font-label text-sm uppercase tracking-widest hover:text-primary transition-colors">
                        Browse all posts
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endif
        @else
            {{-- No Query State --}}
            <div class="text-center py-20 bg-surface-container-low rounded-xl max-w-2xl mx-auto">
                <svg class="w-16 h-16 mx-auto mb-6 text-outline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-2xl font-headline font-bold text-on-surface mb-3">Enter a search term</h3>
                <p class="text-on-surface-variant font-body mb-8">Type in the search box above to find posts</p>
                <a href="{{ route('posts.index') }}" class="inline-flex items-center justify-center gap-2 text-primary font-label text-sm uppercase tracking-widest hover:underline">
                    Browse all posts
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        @endisset
    </section>
</x-public-layout>
