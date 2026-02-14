<x-layouts::public :title="$page->getSeoTitle()">
    <div class="container mx-auto px-4 py-8">
        <article class="prose lg:prose-xl max-w-none">
            <h1 class="text-4xl font-bold mb-6">{{ $page->title }}</h1>

            @if($page->content)
                <div class="content">
                    {!! $page->content !!}
                </div>
            @endif
        </article>
    </div>
</x-layouts::public>
