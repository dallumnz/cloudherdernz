<x-public-layout>
    {{-- Article Header Section --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 lg:col-span-8 lg:col-start-3 text-center">
                {{-- Category & Reading Time --}}
                <div class="flex justify-center items-center gap-4 mb-6">
                    @if($post->taxonomyTerms->first())
                    <span class="bg-primary-fixed text-primary px-4 py-1 rounded-full font-label text-[10px] uppercase tracking-[0.15em] font-bold">
                        {{ $post->taxonomyTerms->first()->name }}
                    </span>
                    @endif
                    <span class="text-outline text-xs font-label uppercase tracking-widest">
                        {{ $post->content ? ceil(Str::wordCount(strip_tags($post->content)) / 200) . ' min read' : 'Quick read' }}
                    </span>
                </div>
                
                {{-- Title --}}
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-black text-on-surface leading-[1.05] tracking-tight mb-8 letterpress">
                    {{ $post->title }}
                </h1>
                
                {{-- Excerpt --}}
                @if($post->excerpt)
                <p class="text-xl md:text-2xl font-headline italic text-on-surface-variant leading-relaxed max-w-3xl mx-auto">
                    {{ $post->excerpt }}
                </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Featured Image --}}
    @php($featuredMedia = $post->getFirstMedia('featured'))
    @if($featuredMedia)
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 mb-16">
        <div class="relative w-full h-[400px] lg:h-[600px] rounded-xl overflow-hidden">
            <img src="{{ $featuredMedia->getUrl() }}" 
                 alt="{{ $featuredMedia->getCustomProperty('alt_text') ?? $post->title }}" 
                 class="w-full h-full object-cover">
            @if($featuredMedia->getCustomProperty('credit_name'))
            <div class="absolute bottom-6 right-6 bg-surface/90 backdrop-blur px-4 py-2 text-[10px] font-label uppercase tracking-widest text-primary rounded-lg">
                Photo: {{ $featuredMedia->getCustomProperty('credit_name') }}
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Article Body with Sidebars --}}
    <article class="max-w-screen-2xl mx-auto px-6 md:px-8">
        <div class="grid grid-cols-12 gap-8">
            {{-- Sidebar Meta (Desktop) --}}
            <aside class="hidden lg:block col-span-2 col-start-2 pt-4">
                <div class="sticky top-40 space-y-12">
                    {{-- Author --}}
                    <div class="space-y-4">
                        <h4 class="font-label text-[10px] font-bold uppercase tracking-[0.2em] text-primary">Author</h4>
                        <div class="flex flex-col gap-2">
                            <p class="font-headline font-bold text-lg text-on-surface">{{ $post->author?->name ?? 'Admin' }}</p>
                            <p class="text-xs text-on-surface-variant leading-relaxed">{{ $post->author?->bio ? Str::limit($post->author->bio, 80) : 'Writer & Editor' }}</p>
                        </div>
                    </div>
                    
                    {{-- Published Date --}}
                    <div class="space-y-4">
                        <h4 class="font-label text-[10px] font-bold uppercase tracking-[0.2em] text-outline">Published</h4>
                        <p class="font-label text-xs font-medium text-on-surface">{{ $post->published_at?->format('M d, Y') }}</p>
                    </div>
                    
                    {{-- Share --}}
                    <div class="flex flex-col gap-6 pt-4 border-t border-outline-variant/20">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" 
                           target="_blank" 
                           class="text-outline hover:text-primary transition-colors"
                           aria-label="Share on Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://x.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" 
                           target="_blank" 
                           class="text-outline hover:text-primary transition-colors"
                           aria-label="Share on X">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" 
                           target="_blank" 
                           class="text-outline hover:text-primary transition-colors"
                           aria-label="Share on LinkedIn">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                    </div>
                </div>
            </aside>

            {{-- Main Content Column --}}
            <div class="col-span-12 lg:col-span-6">
                {{-- Post Content --}}
                <div class="prose prose-xl max-w-none dark:prose-invert prose-headings:font-headline font-body">
                    @switch($post->post_type)
                        @case('video')
                            @includeWhen($post->postable, 'partials.content.video', ['post' => $post])
                            @break
                        @case('audio')
                            @includeWhen($post->postable, 'partials.content.audio', ['post' => $post])
                            @break
                        @case('image')
                            @includeWhen($post->postable, 'partials.content.gallery', ['post' => $post])
                            @break
                        @default
                            @includeWhen($post->content, 'partials.content.standard', ['post' => $post])
                    @endswitch
                </div>

                {{-- Tags --}}
                @if($post->taxonomyTerms->count() > 1)
                <div class="pt-12 flex flex-wrap gap-3 border-t border-outline-variant/20 mt-12">
                    @foreach($post->taxonomyTerms->skip(1) as $tag)
                    <a href="{{ route('tags.show', $tag->slug) }}" 
                       class="px-4 py-1.5 bg-primary-fixed/50 text-primary font-label text-[10px] uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-colors rounded-lg">
                        {{ $tag->name }}
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Author Bio Card --}}
                <div class="bg-surface-container-low p-8 mt-16 flex flex-col md:flex-row gap-8 items-center md:items-start rounded-xl transition-surface hover:bg-surface-container">
                    @if($post->author?->avatar)
                        <img src="{{ $post->author->avatar }}" alt="{{ $post->author->name }}" class="w-24 h-24 object-cover rounded-full">
                    @else
                        <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center">
                            <span class="text-3xl font-headline font-bold text-primary">{{ substr($post->author?->name ?? 'A', 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="flex-1 text-center md:text-left">
                        <h4 class="font-headline font-bold text-2xl text-on-surface mb-2">{{ $post->author?->name ?? 'Admin' }}</h4>
                        <p class="text-on-surface-variant leading-relaxed mb-4">{{ $post->author?->bio ?? 'Writer and curator exploring the intersection of technology, culture, and thoughtful living.' }}</p>
                        <a href="#" class="text-primary font-label text-xs uppercase tracking-widest font-bold hover:underline underline-offset-4">View All Articles</a>
                    </div>
                </div>
            </div>

            {{-- Right Sidebar (Popular Posts) --}}
            <aside class="col-span-12 lg:col-span-3 space-y-8">
                {{-- Popular Posts --}}
                <div class="bg-surface-container-low rounded-xl p-6 transition-surface hover:bg-surface-container">
                    <h3 class="font-headline text-lg font-bold text-on-surface mb-6 border-b border-outline-variant/20 pb-3">Popular Posts</h3>
                    <div class="space-y-6">
                        @foreach($popularPosts->take(4) as $popular)
                        <article class="group cursor-pointer">
                            <a href="{{ route('posts.show', $popular) }}" class="block">
                                <h4 class="font-headline font-semibold text-on-surface group-hover:text-primary transition-colors leading-snug mb-2">
                                    {{ Str::limit($popular->title, 60) }}
                                </h4>
                                <span class="text-[10px] text-outline uppercase tracking-wider">{{ $popular->published_at?->format('M d, Y') }}</span>
                            </a>
                        </article>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </article>

    {{-- Newsletter Section (Full Width) --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-20">
        <div class="relative overflow-hidden bg-surface-container-low rounded-xl py-16 lg:py-20 px-6 lg:px-12">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-primary-container/20 to-transparent pointer-events-none"></div>
            
            <div class="relative z-10 max-w-3xl mx-auto text-center">
                <h2 class="text-4xl lg:text-5xl font-headline font-bold text-on-surface tracking-tighter leading-tight mb-6 letterpress">
                    Subscribe to <br><span class="italic text-primary">The Weekly Brief</span>
                </h2>
                <p class="text-on-surface-variant text-lg font-body italic mb-8 max-w-xl mx-auto">
                    Our curated selection of long-form essays and discoveries, delivered every Sunday.
                </p>
                
                <div class="max-w-md mx-auto">
                    @livewire('newsletter-subscribe')
                </div>
                <p class="mt-4 text-xs text-outline italic">No spam. Unsubscribe anytime.</p>
            </div>
        </div>
    </section>

    {{-- Comments Section --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-16 border-t border-outline-variant/20">
        <div class="max-w-3xl mx-auto">
            <livewire:comment-thread :post="$post" />
        </div>
    </section>

    {{-- Related Posts --}}
    @isset($relatedPosts)
    @if($relatedPosts->count() > 0)
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-20 border-t border-outline-variant/20">
        <h3 class="text-xs font-label uppercase tracking-[0.3em] font-bold text-outline mb-12">Further Reading</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedPosts->take(3) as $related)
            <a href="{{ route('posts.show', $related) }}" class="group">
                <div class="aspect-[4/5] overflow-hidden mb-6 rounded-xl bg-surface-container-low">
                    @if($related->getFirstMediaUrl('featured'))
                        <img src="{{ $related->getFirstMediaUrl('featured') }}" 
                             alt="{{ $related->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary to-primary-container"></div>
                    @endif
                </div>
                <span class="font-label text-[10px] uppercase tracking-widest text-primary mb-3 block">
                    {{ $related->taxonomyTerms->first()?->name ?? 'Article' }}
                </span>
                <h4 class="text-2xl font-headline font-bold text-on-surface leading-tight group-hover:text-primary transition-colors">
                    {{ Str::limit($related->title, 50) }}
                </h4>
            </a>
            @endforeach
        </div>
    </section>
    @endif
    @endisset
</x-public-layout>
