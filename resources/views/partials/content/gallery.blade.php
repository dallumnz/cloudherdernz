{{-- Gallery/Image content --}}
<div class="mb-6">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        @if($post->media && $post->media->count() > 0)
            @foreach($post->media as $mediaItem)
            <div class="aspect-square bg-slate-200 dark:bg-slate-700 rounded-lg overflow-hidden">
                <img src="{{ $mediaItem->getUrl() }}" alt="{{ $mediaItem->alt ?? $post->title }}" class="w-full h-full object-cover">
            </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-8 text-slate-500 dark:text-slate-400">
                No gallery images available.
            </div>
        @endif
    </div>
    
    @if($post->postable->caption)
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 italic">
        {{ $post->postable->caption }}
    </p>
    @endif
    
    <div class="text-slate-700 dark:text-slate-300 leading-relaxed">
        {!! $post->content !!}
    </div>
</div>
