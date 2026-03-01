<x-layouts::app.sidebar title="Edit Post: {{ $post->title }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Post: {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('posts.update', $post) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Post Type Selection --}}
                        <div>
                            <label for="post_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Post Type</label>
                            <select name="post_type" id="post_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($postTypes as $type)
                                    <option value="{{ $type->value }}" {{ old('post_type', $post->post_type?->value) == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('post_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Excerpt --}}
                        <div>
                            <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Excerpt</label>
                            <textarea name="excerpt" id="excerpt" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('excerpt', $post->excerpt) }}</textarea>
                            @error('excerpt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Content --}}
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content (HTML)</label>
                            <textarea name="content" id="content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('content', $post->content) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Optional: Use this for legacy HTML content or when not using Markdown.</p>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Markdown Content --}}
                        <div>
                            <label for="markdown" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Markdown Content</label>
                            <textarea name="markdown" id="markdown" rows="10" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono">{{ old('markdown', $post->markdown) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Supports GitHub Flavored Markdown (tables, strikethrough, task lists).</p>
                            @error('markdown')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Published At --}}
                        <div>
                            <label for="published_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Published At</label>
                            <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('published_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Taxonomy Terms --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags & Categories</label>
                            <div class="grid grid-cols-2 gap-4 max-h-48 overflow-y-auto p-4 border border-gray-300 dark:border-gray-700 rounded-md">
                                @php
                                    $selectedTerms = old('taxonomy_terms', $post->taxonomyTerms->pluck('id')->toArray());
                                @endphp
                                @foreach($taxonomyTerms as $term)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="taxonomy_terms[]" value="{{ $term->id }}" {{ in_array($term->id, $selectedTerms) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $term->name }} ({{ $term->taxonomy?->name ?? 'Tag' }})</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('taxonomy_terms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type-specific fields --}}
                        @php
                            $currentType = $post->post_type?->value;
                        @endphp
                        <div id="image-fields" class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4 {{ $currentType === 'image' ? '' : 'hidden' }}">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Image Post Settings</h3>
                            <div>
                                <label for="caption" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Caption</label>
                                <textarea name="caption" id="caption" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('caption', $post->postable?->caption ?? '') }}</textarea>
                                @error('caption')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="video-fields" class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4 {{ $currentType === 'video' ? '' : 'hidden' }}">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Video Post Settings</h3>
                            <div>
                                <label for="video_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Video URL</label>
                                <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $post->postable?->video_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('video_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provider</label>
                                <select name="provider" id="provider" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="youtube" {{ old('provider', $post->postable?->provider ?? '') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                    <option value="vimeo" {{ old('provider', $post->postable?->provider ?? '') == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                    <option value="self" {{ old('provider', $post->postable?->provider ?? '') == 'self' ? 'selected' : '' }}>Self-hosted</option>
                                </select>
                                @error('provider')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="duration_seconds" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (seconds)</label>
                                <input type="number" name="duration_seconds" id="duration_seconds" value="{{ old('duration_seconds', $post->postable?->duration_seconds ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('duration_seconds')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="episode_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Episode Number</label>
                                <input type="number" name="episode_number" id="episode_number" value="{{ old('episode_number', $post->postable?->episode_number ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('episode_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="audio-fields" class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4 {{ $currentType === 'audio' ? '' : 'hidden' }}">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Audio Post Settings</h3>
                            <div>
                                <label for="audio_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Audio URL</label>
                                <input type="url" name="audio_url" id="audio_url" value="{{ old('audio_url', $post->postable?->audio_url ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('audio_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="audio_duration_seconds" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (seconds)</label>
                                <input type="number" name="duration_seconds" id="audio_duration_seconds" value="{{ old('duration_seconds', $post->postable?->duration_seconds ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('duration_seconds')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="audio_episode_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Episode Number</label>
                                <input type="number" name="episode_number" id="audio_episode_number" value="{{ old('episode_number', $post->postable?->episode_number ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('episode_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="newsletter-fields" class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4 {{ $currentType === 'newsletter' ? '' : 'hidden' }}">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Newsletter Post Settings</h3>
                            <div>
                                <label for="template" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Template</label>
                                <select name="template" id="template" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="default" {{ old('template', $post->postable?->template ?? '') == 'default' ? 'selected' : '' }}>Default</option>
                                    <option value="minimal" {{ old('template', $post->postable?->template ?? '') == 'minimal' ? 'selected' : '' }}>Minimal</option>
                                    <option value="promotional" {{ old('template', $post->postable?->template ?? '') == 'promotional' ? 'selected' : '' }}>Promotional</option>
                                </select>
                                @error('template')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    Delete Post
                                </button>
                            </form>
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('posts.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition">
                                    Update Post
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const postTypeSelect = document.getElementById('post_type');
            const imageFields = document.getElementById('image-fields');
            const videoFields = document.getElementById('video-fields');
            const audioFields = document.getElementById('audio-fields');
            const newsletterFields = document.getElementById('newsletter-fields');

            function updateFields() {
                const selectedType = postTypeSelect.value;

                imageFields.classList.add('hidden');
                videoFields.classList.add('hidden');
                audioFields.classList.add('hidden');
                newsletterFields.classList.add('hidden');

                if (selectedType === 'image') {
                    imageFields.classList.remove('hidden');
                } else if (selectedType === 'video') {
                    videoFields.classList.remove('hidden');
                } else if (selectedType === 'audio') {
                    audioFields.classList.remove('hidden');
                } else if (selectedType === 'newsletter') {
                    newsletterFields.classList.remove('hidden');
                }
            }

            postTypeSelect.addEventListener('change', updateFields);
            updateFields(); // Run on page load
        });
    </script>
</x-layouts::app.sidebar>
