<x-layouts::app :title="__('Edit Post Type')">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Edit Post Type</h1>

        <form action="{{ route('post-types.update', $postType) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- User implements: form fields -->
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</x-layouts::app>
