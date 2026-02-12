<x-layouts::app :title="__($taxonomyTerm->name)">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">{{ $taxonomyTerm->name }}</h1>

        <p>{{ $taxonomyTerm->description }}</p>

        <div class="mt-4">
            <a href="{{ route('taxonomy-terms.edit', $taxonomyTerm) }}" class="btn btn-secondary">Edit</a>
            <form action="{{ route('taxonomy-terms.destroy', $taxonomyTerm) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</x-layouts::app>
