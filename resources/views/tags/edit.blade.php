<x-layouts::app :title="__('Edit Tag')">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Edit Tag: {{ $tag->name }}</h1>

        <form action="{{ route('tags.update', $tag) }}" method="POST" class="max-w-lg">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $tag->name) }}" class="w-full rounded border-gray-300" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium mb-1">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $tag->slug) }}" class="w-full rounded border-gray-300">
                @error('slug')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded border-gray-300">{{ old('description', $tag->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="btn btn-primary">Update Tag</button>
                <a href="{{ route('tags.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts::app>
