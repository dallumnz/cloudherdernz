@fluxui

<x-layouts::app :title="__('Edit Post')">
    <div class="container mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4">Edit Post: {{ $post->title }}</h1>

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

        <form action="{{ route('posts.update', $post) }}" method="POST" class="max-w-lg space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic Post Information --}}
            <flux:card>
                <h2 class="text-lg font-semibold mb-4 pb-2">Basic Information</h2>

                <div class="space-y-4">
                    <flux:input
                        name="title"
                        id="title"
                        label="Title"
                        value="{{ old('title', $post->title) }}"
                        required
                    />
                    <flux:error name="title" />

                    <flux:input
                        name="slug"
                        id="slug"
                        label="Slug"
                        value="{{ old('slug', $post->slug) }}"
                        required
                    />
                    <flux:error name="slug" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $currentType = $post->post_type?->value ?? 'image';
                        @endphp
                        <flux:field>
                            <flux:label>Post Type</flux:label>
                            <flux:select name="post_type" id="post_type" required onchange="toggleTypeFields()">
                                @foreach ($postTypes as $type)
                                    <option value="{{ $type->value }}" {{ old('post_type', $currentType) == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:error name="post_type" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select name="status" id="status" required>
                                <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </flux:select>
                            <flux:error name="status" />
                        </flux:field>
                    </div>

                    <flux:input
                        type="datetime-local"
                        name="published_at"
                        id="published_at"
                        label="Published At"
                        value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                    />
                    <flux:error name="published_at" />
                </div>
            </flux:card>

            {{-- Type-Specific Fields --}}
            <flux:card>
                <h2 class="text-lg font-semibold mb-4 pb-2">Type-Specific Fields</h2>

                @php
                    $postable = $post->postable;
                @endphp

                {{-- Image Post Fields --}}
                <div id="image-fields" class="type-fields space-y-4">
                    <flux:textarea
                        name="caption"
                        id="caption"
                        label="Caption"
                        rows="3"
                    >{{ old('caption', $postable instanceof \App\Models\ImagePost ? $postable->caption : '') }}</flux:textarea>
                    <flux:error name="caption" />

                    <flux:textarea
                        name="gallery_settings"
                        id="gallery_settings"
                        label="Gallery Settings (JSON)"
                        rows="3"
                        placeholder='{"layout": "grid", "columns": 3}'
                    >{{ old('gallery_settings', $postable instanceof \App\Models\ImagePost && $postable->gallery_settings ? json_encode($postable->gallery_settings) : '') }}</flux:textarea>
                    <flux:error name="gallery_settings" />
                </div>

                {{-- Video Post Fields --}}
                <div id="video-fields" class="type-fields hidden space-y-4">
                    <flux:input
                        type="url"
                        name="video_url"
                        id="video_url"
                        label="Video URL"
                        placeholder="https://youtube.com/watch?v=..."
                        value="{{ old('video_url', $postable instanceof \App\Models\VideoPost ? $postable->video_url : '') }}"
                    />
                    <flux:error name="video_url" />

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $currentProvider = $postable instanceof \App\Models\VideoPost ? $postable->provider : 'self';
                        @endphp
                        <flux:field>
                            <flux:label>Provider</flux:label>
                            <flux:select name="provider" id="provider">
                                <option value="self" {{ old('provider', $currentProvider) == 'self' ? 'selected' : '' }}>Self-hosted</option>
                                <option value="youtube" {{ old('provider', $currentProvider) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ old('provider', $currentProvider) == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                            </flux:select>
                            <flux:error name="provider" />
                        </flux:field>

                        <flux:input
                            type="number"
                            name="duration_seconds"
                            id="duration_seconds"
                            label="Duration (seconds)"
                            min="0"
                            value="{{ old('duration_seconds', $postable instanceof \App\Models\VideoPost ? $postable->duration_seconds : '') }}"
                        />
                        <flux:error name="duration_seconds" />

                        <flux:input
                            type="number"
                            name="episode_number"
                            id="episode_number"
                            label="Episode Number"
                            min="1"
                            value="{{ old('episode_number', $postable instanceof \App\Models\VideoPost ? $postable->episode_number : '') }}"
                        />
                        <flux:error name="episode_number" />
                    </div>

                    <flux:input
                        type="url"
                        name="thumbnail_url"
                        id="thumbnail_url"
                        label="Thumbnail URL"
                        value="{{ old('thumbnail_url', $postable instanceof \App\Models\VideoPost ? $postable->thumbnail_url : '') }}"
                    />
                    <flux:error name="thumbnail_url" />
                </div>

                {{-- Audio Post Fields --}}
                <div id="audio-fields" class="type-fields hidden space-y-4">
                    <flux:input
                        type="url"
                        name="audio_url"
                        id="audio_url"
                        label="Audio URL"
                        placeholder="https://example.com/podcast.mp3"
                        value="{{ old('audio_url', $postable instanceof \App\Models\AudioPost ? $postable->audio_url : '') }}"
                    />
                    <flux:error name="audio_url" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input
                            type="number"
                            name="duration_seconds"
                            id="audio_duration_seconds"
                            label="Duration (seconds)"
                            min="0"
                            value="{{ old('duration_seconds', $postable instanceof \App\Models\AudioPost ? $postable->duration_seconds : '') }}"
                        />
                        <flux:error name="duration_seconds" />

                        <flux:input
                            type="number"
                            name="episode_number"
                            id="audio_episode_number"
                            label="Episode Number"
                            min="1"
                            value="{{ old('episode_number', $postable instanceof \App\Models\AudioPost ? $postable->episode_number : '') }}"
                        />
                        <flux:error name="episode_number" />
                    </div>
                </div>

                {{-- Newsletter Post Fields --}}
                <div id="newsletter-fields" class="type-fields hidden space-y-4">
                    @php
                        $currentTemplate = $postable instanceof \App\Models\NewsletterPost ? $postable->template : 'default';
                    @endphp
                    <flux:field>
                        <flux:label>Template</flux:label>
                        <flux:select name="template" id="template">
                            <option value="default" {{ old('template', $currentTemplate) == 'default' ? 'selected' : '' }}>Default</option>
                            <option value="minimal" {{ old('template', $currentTemplate) == 'minimal' ? 'selected' : '' }}>Minimal</option>
                            <option value="promotional" {{ old('template', $currentTemplate) == 'promotional' ? 'selected' : '' }}>Promotional</option>
                        </flux:select>
                        <flux:error name="template" />
                    </flux:field>

                    <flux:textarea
                        name="subscriber_settings"
                        id="subscriber_settings"
                        label="Subscriber Settings (JSON)"
                        rows="3"
                        placeholder='{"send_to_all": true, "segments": [], "exclude_unsubscribed": true}'
                    >{{ old('subscriber_settings', $postable instanceof \App\Models\NewsletterPost && $postable->subscriber_settings ? json_encode($postable->subscriber_settings) : '') }}</flux:textarea>
                    <flux:error name="subscriber_settings" />
                </div>
            </flux:card>

            {{-- Content --}}
            <flux:card>
                <h2 class="text-lg font-semibold mb-4 pb-2">Content</h2>

                <div class="space-y-4">
                    <flux:textarea
                        name="excerpt"
                        id="excerpt"
                        label="Excerpt"
                        rows="3"
                        placeholder="Short description of the post"
                    >{{ old('excerpt', $post->excerpt) }}</flux:textarea>
                    <flux:error name="excerpt" />

                    <flux:textarea
                        name="content"
                        id="content"
                        label="Content"
                        rows="10"
                        placeholder="Full post content"
                    >{{ old('content', $post->content) }}</flux:textarea>
                    <flux:error name="content" />
                </div>
            </flux:card>

            {{-- Taxonomy Terms --}}
            <flux:card>
                <h2 class="text-lg font-semibold mb-4 pb-2">Taxonomy</h2>

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

                <flux:error name="taxonomy_terms" />
                <flux:error name="taxonomy_terms.*" />
            </flux:card>

            {{-- Submit --}}
            <div class="flex items-center space-x-4">
                <flux:button type="submit" variant="primary">Update Post</flux:button>
                <a href="{{ route('posts.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">Cancel</a>
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
