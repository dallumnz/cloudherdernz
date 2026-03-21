<x-public-layout>
    {{-- Tag Header Section --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 lg:col-span-8 lg:col-start-3 text-center">
                {{-- Tag Badge --}}
                <div class="inline-flex items-center justify-center w-16 h-16 bg-tertiary-fixed rounded-full mb-6">
                    <svg class="w-8 h-8 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-on-surface leading-[1.05] tracking-tight mb-6 letterpress">
                    {{ $tag->name }}
                </h1>
                
                @if ($tag->description)
                <p class="text-xl md:text-2xl font-headline italic text-on-surface-variant leading-relaxed max-w-3xl mx-auto mb-6">
                    {{ $tag->description }}
                </p>
                @endif
                
                <p class="text-sm text-outline font-label uppercase tracking-widest">
                    {{ $tag->posts()->count() }} {{ Str::plural('post', $tag->posts()->count()) }}
                </p>
            </div>
        </div>
    </section>

    {{-- Posts Grid --}}
    @php($posts = $tag->posts()->published()->latest('published_at')->paginate(12))

    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-12 border-t border-outline-variant/20">
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
                    <div class="aspect-video bg-gradient-to-br from-tertiary to-tertiary-container flex items-center justify-center">
                        <svg class="w-12 h-12 text-on-tertiary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    @endif
                    <div class="p-6">
                        <h2 class="text-xl font-headline font-bold text-on-surface group-hover:text-primary transition-colors mb-3 leading-tight">
                            {{ $post->title }}
                        </h2>
                        <p class="text-on-surface-variant font-body text-sm line-clamp-3 mb-4">
                            {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 150) }}
                        </p>
                        <div class="flex items-center justify-between pt-4 border-t border-outline-variant/20">
                            <span class="text-xs text-outline font-label uppercase tracking-wider">
                                {{ $post->published_at?->format('M d, Y') }}
                            </span>
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
        {{-- Empty State --}}
        <div class="text-center py-20 bg-surface-container-low rounded-xl">
            <svg class="w-16 h-16 mx-auto mb-6 text-outline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <h3 class="text-2xl font-headline font-bold text-on-surface mb-3">No posts yet</h3>
            <p class="text-on-surface-variant font-body">No posts are tagged with "{{ $tag->name }}" yet.</p>
        </div>
        @endif
    </section>

    {{-- Browse More Tags --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-12 border-t border-outline-variant/20">
        <div class="text-center">
            <h3 class="text-lg font-headline font-bold text-on-surface mb-6">Explore Other Topics</h3>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-primary font-label text-sm uppercase tracking-widest hover:underline">
                Back to Home
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </section>
</x-public-layout>
