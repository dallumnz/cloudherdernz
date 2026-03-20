<x-layouts::app>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Send Newsletter</h1>

            @if ($availablePosts->isEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                    <p class="text-yellow-700 dark:text-yellow-300">
                        No available newsletter posts. Create and publish a newsletter post first.
                    </p>
                    <a href="{{ route('admin.posts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Create Post
                    </a>
                </div>
            @else
                <form action="{{ route('admin.newsletter-activities.store') }}" method="POST" class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
                    @csrf

                    <div class="mb-6">
                        <label for="newsletter_post_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Newsletter
                        </label>
                        <select name="newsletter_post_id" id="newsletter_post_id" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-zinc-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            @foreach ($availablePosts as $newsletterPost)
                                @php $post = $newsletterPost->posts->first(); @endphp
                                @if($post)
                                    <option value="{{ $newsletterPost->id }}">{{ $post->title }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            This will send to <strong>{{ $confirmedSubscriberCount }}</strong> confirmed subscribers.
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_test" value="1" id="is_test"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Send test email first</span>
                        </label>
                    </div>

                    <div class="mb-6 hidden" id="test_email_container">
                        <label for="test_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Test Email Address
                        </label>
                        <input type="email" name="test_email" id="test_email"
                               value="{{ auth()->user()->email }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-zinc-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.newsletter-activities.index') }}" 
                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Send Newsletter
                        </button>
                    </div>
                </form>

                <script>
                    document.getElementById('is_test').addEventListener('change', function() {
                        document.getElementById('test_email_container').classList.toggle('hidden', !this.checked);
                    });
                </script>
            @endif
        </div>
    </div>
</x-layouts::app>
