<x-layouts::app :title="__($postType->name)">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">{{ $postType->name }}</h1>

        <p>{{ $postType->description }}</p>

        <div class="mt-4">
            <a href="{{ route('post-types.edit', $postType) }}" class="btn btn-secondary">Edit</a>
            <form action="{{ route('post-types.destroy', $postType) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</x-layouts::app>
