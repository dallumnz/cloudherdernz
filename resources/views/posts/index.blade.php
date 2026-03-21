<x-public-layout>
    {{-- Category Filter Buttons --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-12">
        <h1 class="text-4xl lg:text-5xl font-headline font-bold tracking-tighter mb-8 letterpress">All Chronicles</h1>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('posts.index') }}" 
               class="px-5 py-2 rounded-lg transition-all font-label text-sm {{ request()->routeIs('posts.index') && !request('category') ? 'bg-primary text-on-primary' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high border border-outline-variant/20' }}">
                All
            </a>
            @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}" 
               class="px-5 py-2 rounded-lg transition-all font-label text-sm bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high hover:text-primary border border-outline-variant/20">
                {{ $category->name }}
            </a>
            @endforeach
        </div>
    </section>

    {{-- Featured/First Posts Bento Grid --}}
    @if($posts->count() > 0)
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Large Featured Card --}}
            @if($posts->first())
            <div class="lg:col-span-2 lg:row-span-2">
                <article class="h-full bg-surface-container-low rounded-lg overflow-hidden transition-surface hover:bg-surface-container-high group cursor-pointer">
                    <a href="{{ route('posts.show', $posts->first()) }}" class="block h-full">
                        <div class="aspect-[4/3] lg:aspect-auto lg:h-80 w-full overflow-hidden">
                            @if($posts->first()->getFirstMediaUrl('featured'))
                                <img src="{{ $posts->first()->getFirstMediaUrl('featured') }}" 
                                     alt="{{ $posts->first()->title }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary to-primary-container"></div>
                            @endif
                        </div>
                        <div class="p-6 lg:p-8">
                            <span class="text-tertiary text-xs font-label uppercase tracking-widest mb-3 block">
                                {{ $posts->first()->taxonomyTerms->first()?->name ?? 'Featured' }}
                            </span>
                            <h2 class="text-2xl lg:text-3xl font-headline font-bold text-on-surface group-hover:text-primary transition-colors leading-tight mb-4">
                                {{ $posts->first()->title }}
                            </h2>
                            <p class="text-on-surface-variant font-body mb-4 line-clamp-3">
                                {{ $posts->first()->excerpt ?? Str::limit(strip_tags($posts->first()->content), 200) }}
                            </p>
                            <div class="flex items-center gap-4 text-sm text-outline">
                                <span>By {{ $posts->first()->author?->name ?? 'Admin' }}</span>
                                <span>•</span>
                                <span>{{ $posts->first()->published_at?->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </a>
                </article>
            </div>
            @endif

            {{-- Medium Cards --}}
            @foreach($posts->slice(1, 2) as $post)
            <div class="lg:col-span-2">
                <article class="h-full bg-surface-container-low rounded-lg overflow-hidden transition-surface hover:bg-surface-container-high group cursor-pointer">
                    <a href="{{ route('posts.show', $post) }}" class="block h-full">
                        <div class="flex flex-col md:flex-row h-full">
                            <div class="w-full md:w-48 lg:w-56 h-48 md:h-auto flex-shrink-0 overflow-hidden">
                                @if($post->getFirstMediaUrl('featured'))
                                    <img src="{{ $post->getFirstMediaUrl('featured') }}" 
                                         alt="{{ $post->title }}" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary to-primary-container"></div>
                                @endif
                            </div>
                            <div class="p-6 flex flex-col justify-center flex-1">
                                <span class="text-primary text-xs font-label italic mb-2">
                                    {{ $post->taxonomyTerms->first()?->name ?? 'Post' }}
                                </span>
                                <h3 class="text-xl font-headline font-bold text-on-surface group-hover:text-primary transition-colors leading-tight mb-2">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-on-surface-variant text-sm mb-3 line-clamp-2">
                                    {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}
                                </p>
                                <span class="text-xs text-outline">{{ $post->published_at?->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </a>
                </article>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Posts List --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Posts Grid --}}
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-headline font-bold tracking-tighter mb-8 letterpress pb-4 border-b border-outline-variant/20">
                    More Stories
                </h2>
                <div class="space-y-8">
                    @forelse($posts->slice(3) as $post)
                    <article class="bg-surface-container-low rounded-lg overflow-hidden transition-surface hover:bg-surface-container-high group cursor-pointer">
                        <a href="{{ route('posts.show', $post) }}" class="block">
                            <div class="flex flex-col md:flex-row">
                                @php($featuredMedia = $post->getFirstMedia('featured'))
                                <div class="w-full md:w-72 h-48 md:h-auto flex-shrink-0 overflow-hidden bg-gradient-to-br from-primary to-primary-container">
                                    @if($featuredMedia)
                                        <img src="{{ $featuredMedia->getUrl() }}" 
                                             alt="{{ $featuredMedia->getCustomProperty('alt_text') ?? $post->title }}" 
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    @endif
                                </div>
                                <div class="p-6 flex flex-col justify-center flex-1">
                                    <span class="text-primary text-xs font-label italic mb-2">
                                        {{ $post->taxonomyTerms->first()?->name ?? 'Post' }}
                                    </span>
                                    <h3 class="text-xl font-headline font-bold text-on-surface group-hover:text-primary transition-colors mt-1 mb-3 leading-tight">
                                        {{ $post->title }}
                                    </h3>
                                    <p class="text-on-surface-variant font-body text-sm mb-4 line-clamp-2">
                                        {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150) }}
                                    </p>
                                    <div class="flex items-center justify-between text-sm text-outline">
                                        <span>By {{ $post->author?->name ?? 'Admin' }}</span>
                                        <span>{{ $post->published_at?->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </article>
                    @empty
                    <div class="text-center py-16">
                        <p class="text-on-surface-variant font-headline italic text-xl">No more posts.</p>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-8">
                {{-- Search --}}
                <div class="bg-surface-container-low rounded-xl p-6 transition-surface hover:bg-surface-container">
                    <h3 class="font-headline text-lg font-bold text-on-surface mb-4">Search</h3>
                    <form action="{{ route('search.results') }}" method="GET" class="space-y-4">
                        <input 
                            type="text" 
                            name="q"
                            placeholder="Search articles..." 
                            class="w-full px-4 py-3 bg-surface border-none focus:ring-1 focus:ring-primary/40 rounded-lg text-on-surface font-body placeholder:text-outline/60"
                        >
                        <button type="submit" class="w-full px-4 py-2 bg-primary hover:bg-primary-container text-on-primary font-bold rounded-lg transition-colors">
                            Search
                        </button>
                    </form>
                </div>

                {{-- Popular Tags --}}
                <div class="bg-surface-container-low rounded-xl p-6 transition-surface hover:bg-surface-container">
                    <h3 class="font-headline text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/20 pb-3">Popular Topics</h3>
                    <div class="space-y-4">
                        @foreach($popularTags->take(6) as $tag)
                        <div class="flex items-center justify-between group cursor-pointer">
                            <a href="{{ route('tags.show', $tag->slug) }}" class="font-label text-sm text-on-surface group-hover:text-primary transition-colors">
                                {{ $tag->name }}
                            </a>
                            <span class="text-xs text-outline">{{ $tag->posts_count }} posts</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Newsletter --}}
                <div class="bg-primary-fixed rounded-xl p-6">
                    <h3 class="font-headline text-lg font-bold text-primary mb-2">Stay Updated</h3>
                    <p class="text-on-surface-variant font-body text-sm mb-4">Subscribe for the latest chronicles.</p>
                    @livewire('newsletter-subscribe')
                </div>
            </aside>
        </div>
    </section>
</x-public-layout>
