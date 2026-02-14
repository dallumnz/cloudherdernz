<x-layouts::app :title="__($page->title)">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">{{ $page->title }}</h1>

        <div class="bg-white shadow-md rounded p-6 mb-4">
            <div class="mb-4">
                <strong>Slug:</strong> {{ $page->slug }}
            </div>
            <div class="mb-4">
                <strong>Status:</strong> {{ $page->status }}
            </div>
            <div class="mb-4">
                <strong>Content:</strong>
                <div class="mt-2 prose">{!! $page->content !!}</div>
            </div>
            @if($page->meta_title)
                <div class="mb-4">
                    <strong>Meta Title:</strong> {{ $page->meta_title }}
                </div>
            @endif
            @if($page->meta_description)
                <div class="mb-4">
                    <strong>Meta Description:</strong> {{ $page->meta_description }}
                </div>
            @endif
        </div>

        <div class="flex space-x-4">
            <a href="{{ route('admin.pages.edit', $page) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Delete
                </button>
            </form>
            <a href="{{ route('admin.pages.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </div>
</x-layouts::app>
