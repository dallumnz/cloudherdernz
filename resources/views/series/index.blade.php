<x-public-layout>
    {{-- Series Index Header --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-on-surface tracking-tight mb-6 letterpress">
                Series
            </h1>
            <p class="text-xl md:text-2xl text-on-surface-variant font-body italic">
                Curated collections of posts on a single theme.
            </p>
        </div>
    </section>

    {{-- Series Grid --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-20">
        @if($series->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($series as $seriesItem)
                    <article class="bg-surface-container-low rounded-lg overflow-hidden transition-surface hover:bg-surface-container-high group cursor-pointer">
                        <a href="{{ route('series.show', $seriesItem->slug) }}" class="block h-full">
                            <div class="aspect-video w-full overflow-hidden bg-gradient-to-br from-primary to-primary-container">
                                @if($seriesItem->getFirstMediaUrl('featured'))
                                    <img src="{{ $seriesItem->getFirstMediaUrl('featured') }}"
                                         alt="{{ $seriesItem->title }}"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @endif
                            </div>
                            <div class="p-6">
                                <h2 class="text-xl font-headline font-bold text-on-surface group-hover:text-primary transition-colors leading-tight mb-2">
                                    {{ $seriesItem->title }}
                                </h2>
                                @if($seriesItem->tagline)
                                    <p class="text-on-surface-variant font-body text-sm line-clamp-2">
                                        {{ $seriesItem->tagline }}
                                    </p>
                                @endif
                                <p class="mt-4 text-sm text-outline font-label">
                                    {{ $seriesItem->posts()->count() }} {{ \Illuminate\Support\Str::plural('post', $seriesItem->posts()->count()) }}
                                </p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $series->links() }}
            </div>
        @else
            <div class="max-w-4xl mx-auto text-center py-16">
                <p class="text-on-surface-variant font-body text-lg">No series have been published yet.</p>
            </div>
        @endif
    </section>
</x-public-layout>
