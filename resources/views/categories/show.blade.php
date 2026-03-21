<x-public-layout>
    {{-- Category Header Section --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 lg:col-span-8 lg:col-start-3 text-center">
                {{-- Category Badge --}}
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-fixed rounded-full mb-6">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-on-surface leading-[1.05] tracking-tight mb-6 letterpress">
                    {{ $category->name }}
                </h1>
                
                @if ($category->description)
                <p class="text-xl md:text-2xl font-headline italic text-on-surface-variant leading-relaxed max-w-3xl mx-auto mb-6">
                    {{ $category->description }}
                </p>
                @endif
                
                <p class="text-sm text-outline font-label uppercase tracking-widest">
                    {{ $category->posts()->count() }} {{ Str::plural('post', $category->posts()->count()) }}
                </p>
            </div>
        </div>
    </section>

    {{-- Parent Category Link --}}
    @if ($category->parent)
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-8 text-center">
        <a href="{{ route('categories.show', $category->parent) }}" class="inline-flex items-center text-primary hover:text-primary-container font-label text-sm uppercase tracking-widest transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to {{ $category->parent->name }}
        </a>
    </section>
    @endif

    {{-- Subcategories --}}
    @if ($category->children->count() > 0)
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-8 border-b border-outline-variant/20">
        <h2 class="text-xl font-headline font-bold tracking-tighter mb-6 text-center letterpress">Subcategories</h2>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach ($category->children as $child)
            <a href="{{ route('categories.show', $child) }}" class="inline-flex items-center px-5 py-2.5 rounded-lg font-label text-sm bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high hover:text-primary border border-outline-variant/20 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                {{ $child->name }}
                <span class="ml-2 text-xs text-outline">({{ $child->posts()->count() }})</span>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Posts Grid --}}
    @php($posts = $category->posts()->published()->latest('published_at')->paginate(12))

    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 py-12">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-2xl font-headline font-bold text-on-surface mb-3">No posts yet</h3>
            <p class="text-on-surface-variant font-body">No posts in "{{ $category->name }}" yet. Check back soon!</p>
        </div>
        @endif
    </section>
</x-public-layout>
