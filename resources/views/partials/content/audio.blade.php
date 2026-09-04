{{-- Audio content --}}
<div class="mb-6">
    @if($post->postable->embed_url)
        @if(str_contains($post->postable->embed_url, 'open.spotify.com'))
            <div class="rounded-xl overflow-hidden mb-4">
                <iframe
                    style="border-radius:12px"
                    src="{{ $post->postable->embed_url }}"
                    width="100%"
                    height="152"
                    frameborder="0"
                    allowfullscreen=""
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy">
                </iframe>
            </div>
        @else
            <div class="bg-slate-100 dark:bg-slate-800 rounded-xl p-6 mb-4">
                <audio controls class="w-full">
                    <source src="{{ $post->postable->embed_url }}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        @endif
    @endif
    <div class="text-slate-700 dark:text-slate-300 leading-relaxed">
        {!! $post->content_html !!}
    </div>
</div>
