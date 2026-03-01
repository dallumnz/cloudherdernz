<div
    x-data="{
        easyMDE: null,
        isFullscreen: @entangle('isFullscreen'),
        init() {
            this.initEasyMDE();
            this.$wire.on('toggle-fullscreen', (event) => {
                if (event[0].isFullscreen) {
                    this.easyMDE.toggleFullScreen();
                } else if (this.easyMDE.isFullscreenActive()) {
                    this.easyMDE.toggleFullScreen();
                }
            });
            this.$wire.on('clear-message', () => {
                setTimeout(() => this.$wire.clearMessage(), 3000);
            });
        },
        initEasyMDE() {
            const self = this;
            this.easyMDE = new EasyMDE({
                element: this.$refs.textarea,
                autofocus: true,
                autosave: {
                    enabled: true,
                    uniqueId: 'post-{{ $postId ?? 'new' }}',
                    delay: 1000,
                },
                spellChecker: false,
                placeholder: 'Write your content in Markdown...',
                toolbar: [
                    'bold', 'italic', 'heading', '|',
                    'quote', 'unordered-list', 'ordered-list', '|',
                    'link', 'image', {
                        name: 'upload-image',
                        action: function customFunction(editor) {
                            self.openImageUpload();
                        },
                        className: 'fa fa-picture-o',
                        title: 'Upload Image',
                    }, '|',
                    'preview', 'side-by-side', 'fullscreen', '|',
                    'guide'
                ],
                previewRender: function(plainText, preview) {
                    // Trigger Livewire update for preview
                    self.$wire.$set('content', plainText);
                    return preview.innerHTML;
                },
                onChange: function() {
                    // Debounced auto-save
                    clearTimeout(self.saveTimeout);
                    self.saveTimeout = setTimeout(() => {
                        self.$wire.autoSave();
                    }, 2000);
                }
            });

            // Set initial content
            if (this.$wire.content) {
                this.easyMDE.value(this.$wire.content);
            }
        },
        openImageUpload() {
            // Dispatch event to open media uploader modal
            this.$dispatch('open-media-uploader', {
                callback: 'insertImage',
                multiple: false
            });
        },
        insertImage(url, alt = '') {
            const cm = this.easyMDE.codemirror;
            const cursor = cm.getCursor();
            const markdown = `![${alt}](${url})`;
            cm.replaceSelection(markdown);
            this.$wire.handleImageUpload(url, alt);
        },
        destroy() {
            if (this.easyMDE) {
                this.easyMDE.toTextArea();
                this.easyMDE = null;
            }
        }
    }"
    x-init="init()"
    x-on:media-selected.window="insertImage($event.detail.url, $event.detail.alt || '')"
    @class([
        'markdown-editor-wrapper',
        'fixed inset-0 z-50 bg-white dark:bg-gray-900' => $isFullscreen,
        'relative' => ! $isFullscreen,
    ])
>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-4">
            <flux:heading size="lg">Markdown Editor</flux:heading>
            @if ($title)
                <flux:text variant="secondary" size="sm">Editing: {{ $title }}</flux:text>
            @endif
        </div>

        <div class="flex items-center gap-3">
            {{-- Stats --}}
            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <span>{{ $this->wordCount }} words</span>
                <span>{{ $this->characterCount }} chars</span>
            </div>

            {{-- Save Status --}}
            @if ($isSaving)
                <flux:text size="sm" variant="secondary">
                    <span class="flex items-center gap-1">
                        <flux:icon name="arrow-path" class="w-4 h-4 animate-spin" />
                        Saving...
                    </span>
                </flux:text>
            @elseif ($lastSavedAt)
                <flux:text size="sm" variant="secondary">
                    Saved {{ $lastSavedAt }}
                </flux:text>
            @endif

            {{-- Fullscreen Toggle --}}
            <flux:button
                type="button"
                wire:click="toggleFullscreen"
                variant="ghost"
                size="sm"
                aria-label="Toggle fullscreen mode"
                title="Toggle fullscreen"
            >
                @if ($isFullscreen)
                    <flux:icon name="arrows-pointing-in" class="w-5 h-5" aria-hidden="true" />
                @else
                    <flux:icon name="arrows-pointing-out" class="w-5 h-5" aria-hidden="true" />
                @endif
            </flux:button>

            {{-- Save Button --}}
            <flux:button
                type="button"
                wire:click="save"
                variant="primary"
                size="sm"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">Save Content</span>
                <span wire:loading wire:target="save">Saving...</span>
            </flux:button>
        </div>
    </div>

    {{-- Messages --}}
    @if ($message)
        <flux:callout
            variant="{{ $messageType === 'success' ? 'success' : 'error' }}"
            class="mb-4"
        >
            {{ $message }}
        </flux:callout>
    @endif

    {{-- Editor Container --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-full" style="min-height: 500px;">
        {{-- Markdown Input --}}
        <div class="flex flex-col">
            <flux:text variant="secondary" size="sm" class="mb-2">Markdown</flux:text>
            <div class="flex-1 relative">
                <textarea
                    x-ref="textarea"
                    wire:model.live.debounce.500ms="content"
                    class="w-full h-full min-h-[400px] p-4 font-mono text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg resize-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Write your content in Markdown..."
                ></textarea>
            </div>
        </div>

        {{-- Live Preview --}}
        <div class="flex flex-col">
            <flux:text variant="secondary" size="sm" class="mb-2">Preview</flux:text>
            <div class="flex-1 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-y-auto min-h-[400px]">
                @if ($previewHtml)
                    <article class="prose dark:prose-invert max-w-none">
                        {!! $previewHtml !!}
                    </article>
                @else
                    <div class="text-gray-400 dark:text-gray-500 text-center py-12">
                        <flux:icon name="eye" class="w-12 h-12 mx-auto mb-4 opacity-50" />
                        <p>Preview will appear here...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Help Text --}}
    <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
        <div class="flex items-center gap-4">
            <span>Supports Markdown syntax</span>
            <a
                href="https://www.markdownguide.org/basic-syntax/"
                target="_blank"
                rel="noopener noreferrer"
                class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
            >
                Markdown Guide
            </a>
        </div>
        <div class="flex items-center gap-4">
            <span>Status: <span class="capitalize">{{ $status }}</span></span>
            @if ($postId)
                <flux:button
                    href="{{ route('posts.show', $postId) }}"
                    target="_blank"
                    variant="ghost"
                    size="sm"
                >
                    View Post
                </flux:button>
            @endif
        </div>
    </div>
</div>

{{-- EasyMDE Styles --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <style>
        .EasyMDEContainer {
            border-radius: 0.5rem;
            border: 1px solid rgb(229 231 235);
        }
        .dark .EasyMDEContainer {
            border-color: rgb(55 65 81);
        }
        .EasyMDEContainer .CodeMirror {
            background-color: rgb(249 250 251);
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .dark .EasyMDEContainer .CodeMirror {
            background-color: rgb(31 41 55);
            color: rgb(209 213 219);
        }
        .EasyMDEContainer .editor-toolbar {
            border-radius: 0.5rem 0.5rem 0 0;
            border-bottom: 1px solid rgb(229 231 235);
        }
        .dark .EasyMDEContainer .editor-toolbar {
            background-color: rgb(31 41 55);
            border-color: rgb(55 65 81);
        }
        .EasyMDEContainer .editor-toolbar button {
            color: rgb(75 85 99);
        }
        .dark .EasyMDEContainer .editor-toolbar button {
            color: rgb(209 213 219);
        }
        .EasyMDEContainer .editor-toolbar button:hover {
            background-color: rgb(243 244 246);
        }
        .dark .EasyMDEContainer .editor-toolbar button:hover {
            background-color: rgb(55 65 81);
        }
        .EasyMDEContainer .CodeMirror-fullscreen,
        .EasyMDEContainer .editor-preview-side {
            z-index: 51;
        }
        .editor-toolbar.fullscreen {
            z-index: 52;
        }
    </style>
@endpush

{{-- EasyMDE Script --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
@endpush
