<x-layouts::app :title="__('Post Types')">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Post Types</h1>

        <a href="{{ route('post-types.create') }}" class="btn btn-primary mb-4">Create Post Type</a>

        @foreach ($postTypes as $postType)
            <div class="card mb-4">
                <h2 class="text-xl font-semibold">
                    <a href="{{ route('post-types.show', $postType) }}">{{ $postType->name }}</a>
                </h2>
                <p class="text-gray-600">{{ $postType->description }}</p>
            </div>
        @endforeach

        {{ $postTypes->links() }}
    </div>
</x-layouts::app>
