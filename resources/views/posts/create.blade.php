<x-layouts::app :title="__('Create Post')">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Create Post</h1>
            <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800">
                &larr; Back to Posts
            </a>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Basic Post Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-semibold border-b pb-2">Basic Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="leave-empty-for-auto">
                        <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from title</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Post Type --}}
                    <div>
                        <label for="post_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Post Type <span class="text-red-500">*</span>
                        </label>
                        <select name="post_type" id="post_type" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onchange="toggleTypeFields()">
                            @foreach ($postTypes as $type)
                                <option value="{{ $type->value }}" {{ old('post_type') == $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('post_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Published At --}}
                <div>
                    <label for="published_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Published At
                    </label>
                    <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at') }}"
                        class="w-full md:w-1/2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">When to publish (optional)</p>
                    @error('published_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Type-Specific Fields --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-semibold border-b pb-2">Type-Specific Fields</h2>

                {{-- Image Post Fields --}}
                <div id="image-fields" class="type-fields space-y-4">
                    <div>
                        <label for="caption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Caption
                        </label>
                        <textarea name="caption" id="caption" rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('caption') }}</textarea>
                        @error('caption')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gallery_settings" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Gallery Settings (JSON)
                        </label>
                        <textarea name="gallery_settings" id="gallery_settings" rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                            placeholder='{"layout": "grid", "columns": 3}'>{{ old('gallery_settings') }}</textarea>
                        @error('gallery_settings')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Video Post Fields --}}
                <div id="video-fields" class="type-fields hidden space-y-4">
                    <div>
                        <label for="video_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Video URL <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://youtube.com/watch?v=...">
                        @error('video_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Provider
                            </label>
                            <select name="provider" id="provider"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="self" {{ old('provider', 'self') == 'self' ? 'selected' : '' }}>Self-hosted</option>
                                <option value="youtube" {{ old('provider') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ old('provider') == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                            </select>
                            @error('provider')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_seconds" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Duration (seconds)
                            </label>
                            <input type="number" name="duration_seconds" id="duration_seconds" value="{{ old('duration_seconds') }}" min="0"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('duration_seconds')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="episode_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Episode Number
                            </label>
                            <input type="number" name="episode_number" id="episode_number" value="{{ old('episode_number') }}" min="1"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('episode_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="thumbnail_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Thumbnail URL
                        </label>
                        <input type="url" name="thumbnail_url" id="thumbnail_url" value="{{ old('thumbnail_url') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('thumbnail_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Audio Post Fields --}}
                <div id="audio-fields" class="type-fields hidden space-y-4">
                    <div>
                        <label for="audio_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Audio URL <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://example.com/podcast.mp3">
                        @error('audio_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="audio_duration_seconds" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Duration (seconds)
                            </label>
                            <input type="number" name="duration_seconds" id="audio_duration_seconds" value="{{ old('duration_seconds') }}" min="0"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('duration_seconds')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="audio_episode_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Episode Number
                            </label>
                            <input type="number" name="episode_number" id="audio_episode_number" value="{{ old('episode_number') }}" min="1"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('episode_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Newsletter Post Fields --}}
                <div id="newsletter-fields" class="type-fields hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="template" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Template
                            </label>
                            <select name="template" id="template"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="default" {{ old('template', 'default') == 'default' ? 'selected' : '' }}>Default</option>
                                <option value="minimal" {{ old('template') == 'minimal' ? 'selected' : '' }}>Minimal</option>
                                <option value="promotional" {{ old('template') == 'promotional' ? 'selected' : '' }}>Promotional</option>
                            </select>
                            @error('template')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subscriber_settings" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Subscriber Settings (JSON)
                        </label>
                        <textarea name="subscriber_settings" id="subscriber_settings" rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                            placeholder='{"send_to_all": true, "segments": [], "exclude_unsubscribed": true}'>{{ old('subscriber_settings') }}</textarea>
                        @error('subscriber_settings')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-semibold border-b pb-2">Content</h2>

                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Excerpt
                    </label>
                    <textarea name="excerpt" id="excerpt" rows="3"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Short description of the post">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Content
                    </label>
                    <textarea name="content" id="content" rows="10"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Full post content">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Taxonomy Terms --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-semibold border-b pb-2">Taxonomy</h2>

                @php
                    $tags = $taxonomyTerms->where('taxonomy.type', 'tag');
                    $categories = $taxonomyTerms->where('taxonomy.type', 'category');
                @endphp

                @if ($tags->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <label class="inline-flex items-center px-3 py-1 rounded-full text-sm cursor-pointer transition-colors
                                    {{ in_array($tag->id, old('taxonomy_terms', [])) ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}
                                    hover:bg-blue-50 dark:hover:bg-blue-900/50">
                                    <input type="checkbox" name="taxonomy_terms[]" value="{{ $tag->id }}"
                                        {{ in_array($tag->id, old('taxonomy_terms', [])) ? 'checked' : '' }}
                                        class="sr-only">
                                    {{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($categories->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Categories</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <label class="inline-flex items-center px-3 py-1 rounded-full text-sm cursor-pointer transition-colors
                                    {{ in_array($category->id, old('taxonomy_terms', [])) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}
                                    hover:bg-green-50 dark:hover:bg-green-900/50">
                                    <input type="checkbox" name="taxonomy_terms[]" value="{{ $category->id }}"
                                        {{ in_array($category->id, old('taxonomy_terms', [])) ? 'checked' : '' }}
                                        class="sr-only">
                                    {{ $category->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @error('taxonomy_terms')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('taxonomy_terms.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center space-x-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Create Post
                </button>
                <a href="{{ route('posts.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleTypeFields() {
            const type = document.getElementById('post_type').value;

            // Hide all type-specific fields
            document.querySelectorAll('.type-fields').forEach(el => el.classList.add('hidden'));

            // Show the selected type's fields
            if (type === 'image') {
                document.getElementById('image-fields').classList.remove('hidden');
            } else if (type === 'video') {
                document.getElementById('video-fields').classList.remove('hidden');
            } else if (type === 'audio') {
                document.getElementById('audio-fields').classList.remove('hidden');
            } else if (type === 'newsletter') {
                document.getElementById('newsletter-fields').classList.remove('hidden');
            }
        }

        // Auto-generate slug from title
        document.getElementById('title').addEventListener('blur', function() {
            const slugField = document.getElementById('slug');
            if (!slugField.value) {
                slugField.value = this.value.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', toggleTypeFields);
    </script>
</x-layouts::app>
