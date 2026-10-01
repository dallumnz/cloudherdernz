<x-public-layout>
    {{-- Series Header --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-on-surface tracking-tight mb-6 letterpress">
                {{ $series->title }}
            </h1>
            @if($series->tagline)
                <p class="text-xl md:text-2xl text-on-surface-variant font-body italic">
                    {{ $series->tagline }}
                </p>
            @endif
        </div>
    </section>

    {{-- Series Description --}}
    @if($series->description_html)
        <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-12">
            <div class="max-w-4xl mx-auto">
                <article class="prose prose-lg max-w-none dark:prose-invert prose-headings:font-headline font-body">
                    {!! clean($series->description_html) !!}
                </article>
            </div>
        </section>
    @endif

    {{-- Series Posts --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-20">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-headline font-bold text-on-surface mb-8">Posts in this series</h2>

            @if($posts->count() > 0)
                <div class="space-y-6">
                    @foreach($posts as $post)
                        <article class="border-b border-outline-variant/20 pb-6">
                            <a href="{{ route('posts.show', $post) }}" class="group block">
                                <h3 class="text-xl font-headline font-semibold text-on-surface group-hover:text-primary transition-colors">
                                    {{ $post->title }}
                                </h3>
                                @if($post->excerpt)
                                    <p class="mt-2 text-on-surface-variant font-body line-clamp-2">
                                        {{ $post->excerpt }}
                                    </p>
                                @endif
                                <p class="mt-2 text-sm text-on-surface-variant/60 font-label">
                                    {{ $post->published_at?->format('M j, Y') }}
                                </p>
                            </a>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @else
                <p class="text-on-surface-variant font-body">No posts have been published in this series yet.</p>
            @endif
        </div>
    </section>
</x-public-layout>
