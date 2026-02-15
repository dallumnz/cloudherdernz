<x-layouts::app :title="__('Edit Post')">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Edit Post: {{ $post->title }}</h1>
            <div class="flex items-center space-x-4">
                <a href="{{ route('posts.show', $post) }}" class="text-green-600 hover:text-green-800" target="_blank">
                    View Post &rarr;
                </a>
                <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800">
                    &larr; Back to Posts
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('posts.update', $post) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Basic Post Information --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Basic Information</h2>

                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required
                        class="w-full rounded border-gray-300">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="slug" class="block text-sm font-medium mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug) }}" required
                        class="w-full rounded border-gray-300">
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="post_type" class="block text-sm font-medium mb-1">Post Type <span class="text-red-500">*</span></label>
                        @php
                            $currentType = $post->post_type?->value ?? 'image';
                        @endphp
                        <select name="post_type" id="post_type" required
                            class="w-full rounded border-gray-300"
                            onchange="toggleTypeFields()">
                            @foreach ($postTypes as $type)
                                <option value="{{ $type->value }}" {{ old('post_type', $currentType) == $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('post_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium mb-1">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="w-full rounded border-gray-300">
                            <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="published_at" class="block text-sm font-medium mb-1">Published At</label>
                    <input type="datetime-local" name="published_at" id="published_at"
                        value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full md:w-1/2 rounded border-gray-300">
                    @error('published_at')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Type-Specific Fields --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Type-Specific Fields</h2>

                @php
                    $postable = $post->postable;
                @endphp

                {{-- Image Post Fields --}}
                <div id="image-fields" class="type-fields mb-4">
                    <div class="mb-4">
                        <label for="caption" class="block text-sm font-medium mb-1">Caption</label>
                        <textarea name="caption" id="caption" rows="3" class="w-full rounded border-gray-300">{{ old('caption', $postable instanceof \App\Models\ImagePost ? $postable->caption : '') }}</textarea>
                        @error('caption')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="gallery_settings" class="block text-sm font-medium mb-1">Gallery Settings (JSON)</label>
                        <textarea name="gallery_settings" id="gallery_settings" rows="3"
                            class="w-full rounded border-gray-300 font-mono text-sm"
                            placeholder='{"layout": "grid", "columns": 3}'>{{ old('gallery_settings', $postable instanceof \App\Models\ImagePost && $postable->gallery_settings ? json_encode($postable->gallery_settings) : '') }}</textarea>
                        @error('gallery_settings')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Video Post Fields --}}
                <div id="video-fields" class="type-fields hidden mb-4">
                    <div class="mb-4">
                        <label for="video_url" class="block text-sm font-medium mb-1">Video URL</label>
                        <input type="url" name="video_url" id="video_url"
                            value="{{ old('video_url', $postable instanceof \App\Models\VideoPost ? $postable->video_url : '') }}"
                            class="w-full rounded border-gray-300"
                            placeholder="https://youtube.com/watch?v=...">
                        @error('video_url')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="provider" class="block text-sm font-medium mb-1">Provider</label>
                            @php
                                $currentProvider = $postable instanceof \App\Models\VideoPost ? $postable->provider : 'self';
                            @endphp
                            <select name="provider" id="provider" class="w-full rounded border-gray-300">
                                <option value="self" {{ old('provider', $currentProvider) == 'self' ? 'selected' : '' }}>Self-hosted</option>
                                <option value="youtube" {{ old('provider', $currentProvider) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ old('provider', $currentProvider) == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                            </select>
                            @error('provider')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_seconds" class="block text-sm font-medium mb-1">Duration (seconds)</label>
                            <input type="number" name="duration_seconds" id="duration_seconds" min="0"
                                value="{{ old('duration_seconds', $postable instanceof \App\Models\VideoPost ? $postable->duration_seconds : '') }}"
                                class="w-full rounded border-gray-300">
                            @error('duration_seconds')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="episode_number" class="block text-sm font-medium mb-1">Episode Number</label>
                            <input type="number" name="episode_number" id="episode_number" min="1"
                                value="{{ old('episode_number', $postable instanceof \App\Models\VideoPost ? $postable->episode_number : '') }}"
                                class="w-full rounded border-gray-300">
                            @error('episode_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="thumbnail_url" class="block text-sm font-medium mb-1">Thumbnail URL</label>
                        <input type="url" name="thumbnail_url" id="thumbnail_url"
                            value="{{ old('thumbnail_url', $postable instanceof \App\Models\VideoPost ? $postable->thumbnail_url : '') }}"
                            class="w-full rounded border-gray-300">
                        @error('thumbnail_url')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Audio Post Fields --}}
                <div id="audio-fields" class="type-fields hidden mb-4">
                    <div class="mb-4">
                        <label for="audio_url" class="block text-sm font-medium mb-1">Audio URL</label>
                        <input type="url" name="audio_url" id="audio_url"
                            value="{{ old('audio_url', $postable instanceof \App\Models\AudioPost ? $postable->audio_url : '') }}"
                            class="w-full rounded border-gray-300"
                            placeholder="https://example.com/podcast.mp3">
                        @error('audio_url')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="audio_duration_seconds" class="block text-sm font-medium mb-1">Duration (seconds)</label>
                            <input type="number" name="duration_seconds" id="audio_duration_seconds" min="0"
                                value="{{ old('duration_seconds', $postable instanceof \App\Models\AudioPost ? $postable->duration_seconds : '') }}"
                                class="w-full rounded border-gray-300">
                            @error('duration_seconds')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="audio_episode_number" class="block text-sm font-medium mb-1">Episode Number</label>
                            <input type="number" name="episode_number" id="audio_episode_number" min="1"
                                value="{{ old('episode_number', $postable instanceof \App\Models\AudioPost ? $postable->episode_number : '') }}"
                                class="w-full rounded border-gray-300">
                            @error('episode_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Newsletter Post Fields --}}
                <div id="newsletter-fields" class="type-fields hidden mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="template" class="block text-sm font-medium mb-1">Template</label>
                            @php
                                $currentTemplate = $postable instanceof \App\Models\NewsletterPost ? $postable->template : 'default';
                            @endphp
                            <select name="template" id="template" class="w-full rounded border-gray-300">
                                <option value="default" {{ old('template', $currentTemplate) == 'default' ? 'selected' : '' }}>Default</option>
                                <option value="minimal" {{ old('template', $currentTemplate) == 'minimal' ? 'selected' : '' }}>Minimal</option>
                                <option value="promotional" {{ old('template', $currentTemplate) == 'promotional' ? 'selected' : '' }}>Promotional</option>
                            </select>
                            @error('template')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="subscriber_settings" class="block text-sm font-medium mb-1">Subscriber Settings (JSON)</label>
                        <textarea name="subscriber_settings" id="subscriber_settings" rows="3"
                            class="w-full rounded border-gray-300 font-mono text-sm"
                            placeholder='{"send_to_all": true, "segments": [], "exclude_unsubscribed": true}'>{{ old('subscriber_settings', $postable instanceof \App\Models\NewsletterPost && $postable->subscriber_settings ? json_encode($postable->subscriber_settings) : '') }}</textarea>
                        @error('subscriber_settings')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Content</h2>

                <div class="mb-4">
                    <label for="excerpt" class="block text-sm font-medium mb-1">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="w-full rounded border-gray-300"
                        placeholder="Short description of the post">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium mb-1">Content</label>
                    <textarea name="content" id="content" rows="10" class="w-full rounded border-gray-300"
                        placeholder="Full post content">{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Taxonomy Terms --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Taxonomy</h2>

                @php
                    $tags = $taxonomyTerms->where('taxonomy.type', 'tag');
                    $categories = $taxonomyTerms->where('taxonomy.type', 'category');
                    $selectedTerms = old('taxonomy_terms', $post->taxonomyTerms->pluck('id')->toArray());
                @endphp

                @if ($tags->count() > 0)
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Tags</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <label class="inline-flex items-center px-3 py-1 rounded-full text-sm cursor-pointer
                                    {{ in_array($tag->id, $selectedTerms) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}">
                                    <input type="checkbox" name="taxonomy_terms[]" value="{{ $tag->id }}"
                                        {{ in_array($tag->id, $selectedTerms) ? 'checked' : '' }}
                                        class="sr-only">
                                    {{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($categories->count() > 0)
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Categories</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <label class="inline-flex items-center px-3 py-1 rounded-full text-sm cursor-pointer
                                    {{ in_array($category->id, $selectedTerms) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    <input type="checkbox" name="taxonomy_terms[]" value="{{ $category->id }}"
                                        {{ in_array($category->id, $selectedTerms) ? 'checked' : '' }}
                                        class="sr-only">
                                    {{ $category->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @error('taxonomy_terms')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('taxonomy_terms.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center space-x-4">
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="{{ route('posts.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', toggleTypeFields);
    </script>
</x-layouts::app>
