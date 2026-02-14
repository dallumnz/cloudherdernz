<x-layouts::public>
    <div class="container mx-auto px-4 py-12">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">All Posts</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">Explore our latest content and stories</p>
        </div>

        {{-- Livewire Search Component --}}
        <livewire:search-posts />
    </div>
</x-layouts::public>
