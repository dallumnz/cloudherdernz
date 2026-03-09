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
                @if($post->getFirstMediaUrl('featured'))
                <div class="mb-6 rounded-xl overflow-hidden">
                    <img src="{{ $post->getFirstMediaUrl('featured') }}" alt="{{ $post->title }}" class="w-full">
                </div>
                @endif

                {{-- Social Share --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-200 dark:border-slate-700">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Share:</span>
                    <a href="#" class="text-slate-400 hover:text-blue-500 transition" aria-label="Share on Twitter" title="Share on Twitter">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-blue-700 transition" aria-label="Share on LinkedIn" title="Share on LinkedIn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-red-500 transition" aria-label="Share on Pinterest" title="Share on Pinterest">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0c-6.627 0-12 5.372-12 12 0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407 1.407-5.965-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
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
                        @php
                            $popularPosts = \App\Models\Post::published()
                                ->with(['author', 'media'])
                                ->latest('published_at')
                                ->take(5)
                                ->get();
                        @endphp
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
