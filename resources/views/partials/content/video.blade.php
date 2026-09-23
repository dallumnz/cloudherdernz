{{-- Video content --}}
<div class="mb-6">
    @if($post->postable->getFirstMedia('video'))
    <div class="aspect-video bg-slate-900 rounded-xl overflow-hidden mb-4">
        <video controls class="w-full h-full" preload="metadata" poster="{{ $post->postable->thumbnail_file_url }}">
            <source src="{{ $post->postable->video_file_url }}" type="video/mp4">
            Your browser does not support the video element.
        </video>
    </div>
    @elseif($post->postable->video_url)
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
        {!! $post->content_html !!}
    </div>
</div>
