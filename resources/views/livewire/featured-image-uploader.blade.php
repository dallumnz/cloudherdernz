<div class="space-y-6">
    <flux:heading size="lg">Featured Image</flux:heading>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}">
            {{ $message }}
        </flux:callout>
    @endif

    {{-- Current Featured Image --}}
    @if ($featuredImage)
        <flux:card>
            <div class="space-y-4">
                <div class="relative">
                    <img
                        src="{{ $featuredImage->getUrl('preview') }}"
                        alt="{{ $featuredImage->name }}"
                        class="w-full h-48 object-cover rounded-lg"
                    >
                    <div class="absolute top-2 right-2">
                        <flux:button
                            wire:click="removeImage({{ $featuredImage->id }})"
                            variant="danger"
                            size="sm"
                        >
                            Remove
                        </flux:button>
                    </div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>Name:</strong> {{ $featuredImage->name }}</p>
                    <p><strong>Size:</strong> {{ number_format($featuredImage->size / 1024, 2) }} KB</p>
                    <p><strong>Type:</strong> {{ $featuredImage->mime_type }}</p>
                </div>
            </div>
        </flux:card>
    @else
        <flux:callout variant="info">
            No featured image set. Upload one below.
        </flux:callout>
    @endif

    {{-- Upload Form --}}
    <flux:card>
        <form wire:submit="save" class="space-y-4">
            <div>
                <flux:label for="image">Upload Featured Image</flux:label>
                <flux:input
                    type="file"
                    id="image"
                    wire:model="image"
                    accept="image/*"
                    class="mt-1"
                />
                <flux:error name="image" />
                <p class="mt-1 text-sm text-gray-500">
                    Recommended size: 1200x630px. Max 10MB. Formats: JPEG, PNG, WebP, AVIF.
                </p>
            </div>

            @if ($image)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700">Preview:</p>
                    <img
                        src="{{ $image->temporaryUrl() }}"
                        alt="Preview"
                        class="mt-2 w-full h-48 object-cover rounded-lg"
                    >
                </div>
            @endif

            <div class="flex items-center gap-4">
                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">Upload Image</span>
                    <span wire:loading wire:target="save">Uploading...</span>
                </flux:button>

                @if ($image)
                    <flux:button
                        type="button"
                        variant="ghost"
                        wire:click="$set('image', null)"
                    >
                        Cancel
                    </flux:button>
                @endif
            </div>
        </form>
    </flux:card>
</div>
