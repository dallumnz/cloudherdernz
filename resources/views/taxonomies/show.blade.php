<x-layouts::app :title="__($taxonomy->name)">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">{{ $taxonomy->name }}</h1>

        <p>{{ $taxonomy->description }}</p>

        <div class="mt-4">
            <a href="{{ route('taxonomies.edit', $taxonomy) }}" class="btn btn-secondary">Edit</a>
            <form action="{{ route('taxonomies.destroy', $taxonomy) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</x-layouts::app>
