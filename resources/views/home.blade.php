<x-public-layout>
    {{-- Hero / Featured Section --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            {{-- Large Featured Post --}}
            @if($featuredPosts->first())
            <div class="lg:col-span-8">
                <a href="{{ route('posts.show', $featuredPosts->first()) }}" class="block group">
                    <div class="relative overflow-hidden rounded-lg">
                        <div class="aspect-[16/9] w-full overflow-hidden">
                            @if($featuredPosts->first()->getFirstMediaUrl('featured'))
                                <img src="{{ $featuredPosts->first()->getFirstMediaUrl('featured') }}" 
                                     alt="{{ $featuredPosts->first()->title }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary to-primary-container"></div>
                            @endif
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-surface/90 via-surface/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-8 lg:p-12 w-full max-w-2xl">
                            <span class="text-tertiary font-label italic text-lg mb-4 block">
                                {{ $featuredPosts->first()->taxonomyTerms->first()?->name ?? 'Featured' }}
                            </span>
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-on-surface leading-[0.95] tracking-tighter mb-6 letterpress">
                                {{ $featuredPosts->first()->title }}
                            </h1>
                            <p class="text-on-surface-variant text-lg lg:text-xl font-body italic max-w-lg mb-8">
                                By {{ $featuredPosts->first()->author?->name ?? 'Admin' }} • {{ $featuredPosts->first()->published_at?->format('M d, Y') }}
                            </p>
                            <span class="inline-flex items-center gap-3 bg-gradient-to-br from-primary to-primary-container px-6 py-3 rounded-lg text-on-primary font-bold transition-transform group-hover:scale-105">
                                Read Article
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            
            {{-- Sidebar: Smaller Featured Posts --}}
            <div class="lg:col-span-4 flex flex-col justify-center">
                @if($featuredPosts->skip(1)->first())
                {{-- Pull Quote Card --}}
                <div class="bg-surface-container-low p-6 lg:p-8 rounded-lg relative transition-surface hover:bg-surface-container-high">
                    <span class="absolute -top-4 -left-2 text-7xl font-headline text-primary/10 select-none">"</span>
                    <p class="text-xl lg:text-2xl font-headline italic text-tertiary mb-4 relative z-10">
                        {{ Str::limit($featuredPosts->first()->excerpt ?? strip_tags($featuredPosts->first()->content), 120) }}
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="h-[1px] w-12 bg-primary"></div>
                        <span class="text-on-surface-variant font-label italic text-sm">{{ $featuredPosts->first()->author?->name ?? 'Admin' }}</span>
                    </div>
                </div>
                @endif
                
                {{-- Top Stories List --}}
                <div class="mt-10 space-y-6">
                    <h3 class="text-primary font-headline text-xl italic border-b border-outline-variant/20 pb-3">Top Stories</h3>
                    
                    @foreach($featuredPosts->skip(1)->take(3) as $post)
                    <article class="space-y-1 group cursor-pointer">
                        <a href="{{ route('posts.show', $post) }}" class="block">
                            <span class="text-tertiary text-xs font-label uppercase tracking-widest">
                                {{ $post->taxonomyTerms->first()?->name ?? 'Article' }}
                            </span>
                            <h4 class="text-lg font-headline font-semibold group-hover:text-primary transition-colors leading-snug">
                                {{ $post->title }}
                            </h4>
                        </a>
                    </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Trending Topics --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-6">
        <h2 class="text-2xl font-headline font-bold tracking-tighter mb-6 letterpress">
            Trending Topics
        </h2>
        <div class="flex flex-wrap gap-3">
            @foreach($popularTags as $tag)
            <a href="{{ route('tags.show', $tag->slug) }}" 
               class="bg-surface-container-low hover:bg-surface-container-high text-on-surface-variant hover:text-primary px-5 py-2 rounded-lg font-label text-sm transition-all border border-outline-variant/20 hover:border-primary/30">
                {{ $tag->name }}
            </a>
            @endforeach
        </div>
    </section>

    {{-- Bento Grid: Recent Stories --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-8">
        <div class="flex items-baseline justify-between mb-10">
            <h2 class="text-3xl lg:text-4xl font-headline font-bold tracking-tighter letterpress">Recent Articles</h2>
            <a href="{{ route('posts.index') }}" class="text-primary italic font-headline text-lg hover:underline flex items-center gap-2">
                Browse all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Large Featured Card --}}
            @if($recentPosts->first())
            <div class="md:col-span-2 md:row-span-2">
                <article class="h-full bg-surface-container-high rounded-lg overflow-hidden flex flex-col group transition-surface hover:bg-surface-container-highest">
                    <a href="{{ route('posts.show', $recentPosts->first()) }}" class="block h-full">
                        <div class="aspect-video w-full overflow-hidden">
                            @if($recentPosts->first()->getFirstMediaUrl('featured'))
                                <img src="{{ $recentPosts->first()->getFirstMediaUrl('featured') }}" 
                                     alt="{{ $recentPosts->first()->title }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary to-primary-container"></div>
                            @endif
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <span class="text-primary text-xs font-label italic mb-2">{{ $recentPosts->first()->taxonomyTerms->first()?->name ?? 'Post' }}</span>
                            <h3 class="text-2xl font-headline font-bold leading-tight mb-3 group-hover:text-primary transition-colors">{{ $recentPosts->first()->title }}</h3>
                            <p class="text-on-surface-variant font-body text-sm mb-4 line-clamp-2">{{ $recentPosts->first()->excerpt ?? Str::limit(strip_tags($recentPosts->first()->content), 150) }}</p>
                            <div class="mt-auto flex items-center justify-between text-xs text-outline">
                                <span>{{ $recentPosts->first()->published_at?->format('M d, Y') }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                </article>
            </div>
            @endif

            {{-- Regular Cards --}}
            @foreach($recentPosts->slice(1, 2) as $post)
            <div class="bg-surface-container-low p-5 rounded-lg transition-surface hover:bg-surface-container-high group cursor-pointer">
                <a href="{{ route('posts.show', $post) }}" class="block">
                    <div class="aspect-square rounded-lg overflow-hidden mb-5 bg-gradient-to-br from-primary/20 to-primary-container/20">
                        @if($post->getFirstMediaUrl('featured'))
                            <img src="{{ $post->getFirstMediaUrl('featured') }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover transition-transform group-hover:scale-110">
                        @endif
                    </div>
                    <span class="text-tertiary text-xs font-label uppercase tracking-widest mb-2 block">{{ $post->taxonomyTerms->first()?->name ?? 'Article' }}</span>
                    <h3 class="text-lg font-headline font-semibold mb-2 group-hover:text-primary transition-colors leading-snug">{{ Str::limit($post->title, 50) }}</h3>
                    <p class="text-on-surface-variant text-sm line-clamp-2">{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 80) }}</p>
                </a>
            </div>
            @endforeach

            {{-- Horizontal Small Card --}}
            @if($recentPosts->slice(3, 1)->first())
            @php($post = $recentPosts->slice(3, 1)->first())
            <div class="md:col-span-2 bg-surface-container-lowest p-5 rounded-lg flex gap-5 items-center transition-surface hover:bg-surface-container-low">
                <a href="{{ route('posts.show', $post) }}" class="flex gap-5 items-center w-full group">
                    <div class="w-28 h-28 flex-shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-primary to-primary-container">
                        @if($post->getFirstMediaUrl('featured'))
                            <img src="{{ $post->getFirstMediaUrl('featured') }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-primary text-xs font-label italic mb-1 block">{{ $post->taxonomyTerms->first()?->name ?? 'Article' }}</span>
                        <h3 class="text-lg font-headline font-bold mb-2 group-hover:text-primary transition-colors leading-snug">{{ $post->title }}</h3>
                        <p class="text-on-surface-variant text-sm line-clamp-2">{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 80) }}</p>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </section>

    {{-- Newsletter Section --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-12">
        <div class="relative overflow-hidden bg-surface-container-low rounded-xl py-16 lg:py-20 px-6 lg:px-12">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-primary-container/20 to-transparent pointer-events-none"></div>
            
            <div class="relative z-10 max-w-3xl mx-auto text-center">
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-on-surface tracking-tighter leading-tight mb-6 letterpress">
                    Subscribe to <br><span class="italic text-primary">The Brief</span>
                </h2>
                <p class="text-on-surface-variant text-lg font-body italic mb-8 max-w-xl mx-auto">
                    Our curated selection of tech news and other discoveries, delivered every month.
                </p>
                
                <div class="max-w-md mx-auto">
                    @livewire('newsletter-subscribe')
                </div>
                <p class="mt-4 text-xs text-outline italic">No spam. Unsubscribe anytime.</p>
            </div>
        </div>
    </section>

    {{-- More Posts List --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-8">
        <h2 class="text-2xl font-headline font-bold tracking-tighter mb-8 letterpress pb-4 border-b border-outline-variant/20">
            More Stories
        </h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Posts Grid --}}
            <div class="lg:col-span-2 space-y-6">
                @foreach($recentPosts->slice(4) as $post)
                <article class="bg-surface-container-low rounded-lg overflow-hidden transition-surface hover:bg-surface-container-high group cursor-pointer">
                    <a href="{{ route('posts.show', $post) }}" class="block">
                        <div class="flex flex-col md:flex-row">
                            <div class="w-full md:w-56 h-48 md:h-auto flex-shrink-0 overflow-hidden">
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
                                <h3 class="text-xl font-headline font-bold text-on-surface group-hover:text-primary transition-colors mt-1 mb-2">
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
                @endforeach
            </div>
        </div>
    </section>
</x-public-layout>
