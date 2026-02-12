<div class="space-y-6">
    <flux:heading size="lg">Gallery Manager</flux:heading>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}">
            {{ $message }}
        </flux:callout>
    @endif

    {{-- Current Gallery Images --}}
    @if ($galleryImages->count() > 0)
        <flux:card>
            <flux:heading size="md" class="mb-4">Gallery Images ({{ $galleryImages->count() }})</flux:heading>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($galleryImages as $image)
                    <div class="relative group" wire:key="gallery-image-{{ $image->id }}">
                        <img
                            src="{{ $image->getUrl('preview') }}"
                            alt="{{ $image->name }}"
                            class="w-full h-32 object-cover rounded-lg"
                        >
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <flux:button
                                wire:click="removeImage({{ $image->id }})"
                                variant="danger"
                                size="sm"
                            >
                                Remove
                            </flux:button>
                        </div>
                        <div class="absolute bottom-1 left-1 right-1 text-xs text-white bg-black bg-opacity-50 rounded px-2 py-1 truncate">
                            {{ $image->name }}
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>
    @else
        <flux:callout variant="info">
            No gallery images yet. Upload some below.
        </flux:callout>
    @endif

    {{-- Upload Form --}}
    <flux:card>
        <form wire:submit="save" class="space-y-4">
            <div>
                <flux:label for="images">Upload Gallery Images</flux:label>
                <flux:input
                    type="file"
                    id="images"
                    wire:model="images"
                    accept="image/*"
                    multiple
                    class="mt-1"
                />
                <flux:error name="images" />
                <flux:error name="images.*" />
                <p class="mt-1 text-sm text-gray-500">
                    You can upload up to 10 images at once. Max 10MB each. Formats: JPEG, PNG, WebP, AVIF.
                </p>
            </div>

            @if (count($images) > 0)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700">Selected Images ({{ count($images) }}):</p>
                    <div class="mt-2 grid grid-cols-4 gap-2">
                        @foreach ($images as $index => $image)
                            <div wire:key="preview-{{ $index }}">
                                @if (method_exists($image, 'temporaryUrl'))
                                    <img
                                        src="{{ $image->temporaryUrl() }}"
                                        alt="Preview {{ $index + 1 }}"
                                        class="w-full h-20 object-cover rounded"
                                    >
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-4">
                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">Upload Images</span>
                    <span wire:loading wire:target="save">Uploading...</span>
                </flux:button>

                @if (count($images) > 0)
                    <flux:button
                        type="button"
                        variant="ghost"
                        wire:click="$set('images', [])"
                    >
                        Clear Selection
                    </flux:button>
                @endif
            </div>
        </form>
    </flux:card>
</div>
