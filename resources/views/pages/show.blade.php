<x-public-layout>
    <x-slot:head>
        <title>{{ $page->getSeoTitle() }} | {{ config('app.name') }}</title>
    </x-slot:head>

    {{-- Page Header --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-on-surface tracking-tight mb-6 letterpress">
                {{ $page->title }}
            </h1>
        </div>
    </section>

    {{-- Page Content --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-20">
        <div class="max-w-4xl mx-auto">
            <article class="prose prose-lg max-w-none dark:prose-invert prose-headings:font-headline font-body">
                @if($page->content)
                    {!! clean($page->content) !!}
                @endif
            </article>
        </div>
    </section>
</x-public-layout>
