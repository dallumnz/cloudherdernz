<x-layouts::app :title="__('Create Taxonomy Term')">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Create Taxonomy Term</h1>

        <form action="{{ route('taxonomy-terms.store') }}" method="POST">
            @csrf
            <!-- User implements: form fields -->
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</x-layouts::app>
