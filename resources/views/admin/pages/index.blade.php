<x-layouts::app :title="__('Pages')">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Pages</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <a href="{{ route('admin.pages.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Page
            </a>
        </div>

        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="py-3 px-6 text-left">Title</th>
                        <th class="py-3 px-6 text-left">Slug</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                        <tr class="border-b">
                            <td class="py-3 px-6">{{ $page->title }}</td>
                            <td class="py-3 px-6">{{ $page->slug }}</td>
                            <td class="py-3 px-6">{{ $page->status }}</td>
                            <td class="py-3 px-6">
                                <a href="{{ route('admin.pages.show', $page) }}" class="text-blue-500 hover:text-blue-700">View</a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-green-500 hover:text-green-700 ml-2">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $pages->links() }}
    </div>
</x-layouts::app>
