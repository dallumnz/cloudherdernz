{{-- Video content --}}
<div class="mb-6">
    @if($post->postable->video_url)
    <div class="aspect-video bg-slate-900 rounded-xl overflow-hidden mb-4">
        <iframe 
            src="{{ $post->postable->video_url }}" 
            class="w-full h-full"
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen>
        </iframe>
    </div>
    @endif
    <div class="text-slate-700 dark:text-slate-300 leading-relaxed">
        {!! $post->content !!}
    </div>
</div>
