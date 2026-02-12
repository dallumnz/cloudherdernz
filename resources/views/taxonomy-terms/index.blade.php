<x-layouts::app :title="__('Taxonomy Terms')">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Taxonomy Terms</h1>

        <a href="{{ route('taxonomy-terms.create') }}" class="btn btn-primary mb-4">Create Term</a>

        @foreach ($terms as $term)
            <div class="card mb-4">
                <h2 class="text-xl font-semibold">
                    <a href="{{ route('taxonomy-terms.show', $term) }}">{{ $term->name }}</a>
                </h2>
                <p class="text-gray-600">{{ $term->taxonomy->name }}</p>
            </div>
        @endforeach

        {{ $terms->links() }}
    </div>
</x-layouts::app>
