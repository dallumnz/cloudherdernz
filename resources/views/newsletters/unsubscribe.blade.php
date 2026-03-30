<x-layouts::app :title="__('Unsubscribe')">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Unsubscribe from Newsletter</h1>
            
            <form action="{{ route('newsletter.unsubscribe') }}" method="POST" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input type="email" name="email" id="email" value="{{ $email ?? '' }}" required
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-zinc-700 dark:text-white">
                </div>
                
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Unsubscribe
                </button>
            </form>
        </div>
    </div>
</x-layouts::app>
