<x-layouts::app :title="__('Taxonomies')">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Taxonomies</h1>

        <a href="{{ route('taxonomies.create') }}" class="btn btn-primary mb-4">Create Taxonomy</a>

        @foreach ($taxonomies as $taxonomy)
            <div class="card mb-4">
                <h2 class="text-xl font-semibold">
                    <a href="{{ route('taxonomies.show', $taxonomy) }}">{{ $taxonomy->name }}</a>
                </h2>
                <p class="text-gray-600">{{ $taxonomy->description }}</p>
            </div>
        @endforeach

        {{ $taxonomies->links() }}
    </div>
</x-layouts::app>
