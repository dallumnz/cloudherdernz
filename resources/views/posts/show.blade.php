<x-public-layout>
    {{-- Main Content Area --}}
    <section class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Post Content --}}
            <article class="lg:col-span-2">
                {{-- Post Header --}}
                <header class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        @if($post->taxonomyTerms->first())
                        <span class="px-3 py-1 bg-indigo-600 text-white text-xs font-medium rounded-full">
                            {{ $post->taxonomyTerms->first()->name }}
                        </span>
                        @endif
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-slate-50 mb-4">
                        {{ $post->title }}
                    </h1>
                    
                    <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                        <span>By {{ $post->author?->name ?? 'Admin' }}</span>
                        <span>•</span>
                        <span>{{ $post->published_at?->format('F d, Y') }}</span>
                        <span>•</span>
                        <span>{{ $post->content ? Str::wordCount(strip_tags($post->content)) . ' words' : '0 words' }}</span>
                    </div>
                </header>

                {{-- Excerpt --}}
                @if($post->excerpt)
                <div class="mb-6 text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                    {{ $post->excerpt }}
                </div>
                @endif

                {{-- Featured Image --}}
                @php($featuredMedia = $post->getFirstMedia('featured'))
                @if($featuredMedia)
                <div class="mb-6">
                    <div class="rounded-xl overflow-hidden">
                        <img src="{{ $featuredMedia->getUrl() }}" alt="{{ $featuredMedia->getCustomProperty('alt_text') ?? $post->title }}" class="w-full">
                    </div>
                    @if($featuredMedia->getCustomProperty('credit_name'))
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 text-right">
                        Photo:
                        @if($featuredMedia->getCustomProperty('credit_url'))
                            <a href="{{ $featuredMedia->getCustomProperty('credit_url') }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $featuredMedia->getCustomProperty('credit_name') }}</a>
                        @else
                            {{ $featuredMedia->getCustomProperty('credit_name') }}
                        @endif
                    </p>
                    @endif
                </div>
                @endif

                {{-- Social Share --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-200 dark:border-slate-700">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-600 transition" aria-label="Share on Facebook" title="Share on Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://x.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-500 transition" aria-label="Share on X" title="Share on X">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-700 transition" aria-label="Share on LinkedIn" title="Share on LinkedIn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                </div>

                {{-- Post Content (Conditional based on post type) --}}
                <div class="prose prose-lg dark:prose-invert text-slate-900 dark:text-slate-100 max-w-none mb-8">
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
                <div class="flex flex-wrap gap-2 pt-6 border-t border-slate-200 dark:border-slate-700">
                    @foreach($post->taxonomyTerms->skip(1) as $tag)
                    <a href="{{ route('tags.show', $tag->slug) }}" class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm rounded-full hover:bg-indigo-600 hover:text-white transition">
                        {{ $tag->name }}
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Comments Section --}}
                <section class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-700">
                    <livewire:comment-thread :post="$post" />
                </section>
            </article>

            {{-- Sidebar --}}
            <aside class="space-y-6">
                {{-- Popular Posts --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-4">Popular Posts</h3>
                    <div class="space-y-4">
                        @foreach($popularPosts as $popular)
                        <a href="{{ route('posts.show', $popular) }}" class="flex gap-3 group">
                            <div class="w-20 h-14 flex-shrink-0 bg-gradient-to-br from-blue-500 to-purple-600 rounded overflow-hidden">
                                @if($popular->getFirstMediaUrl('featured'))
                                    <img src="{{ $popular->getFirstMediaUrl('featured') }}" alt="{{ $popular->title }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-slate-900 dark:text-slate-50 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 line-clamp-2 transition">
                                    {{ $popular->title }}
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $popular->published_at?->format('M d, Y') }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Newsletter --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50 mb-2">Never Miss A Post!</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">Subscribe to our newsletter for the latest updates.</p>
                    <form class="space-y-3">
                        <input 
                            type="email" 
                            placeholder="Your email" 
                            class="w-full px-4 py-2 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500"
                        >
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                            Subscribe
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </section>
</x-public-layout>
