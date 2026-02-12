<x-layouts::public>
    <div class="container mx-auto px-4 py-12">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Tags</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">Browse content by tags</p>
        </div>

        {{-- Tags Cloud --}}
        @if ($tags->count() > 0)
            <div class="max-w-4xl mx-auto">
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach ($tags as $tag)
                        <a
                            href="{{ route('tags.show', $tag) }}"
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-blue-900 dark:hover:text-blue-300 transition"
                        >
                            {{ $tag->name }}
                            <span class="ml-2 text-xs text-gray-500">({{ $tag->posts()->count() }})</span>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $tags->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <flux:icon name="tag" class="w-16 h-16 mx-auto mb-4 text-gray-300" />
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No tags yet</h3>
                <p class="text-gray-500 dark:text-gray-400">Tags will appear here once created.</p>
            </div>
        @endif
    </div>
</x-layouts::public>
