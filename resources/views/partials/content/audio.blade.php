{{-- Audio content --}}
<div class="mb-6">
    @if($post->postable->audio_url)
    <div class="bg-slate-100 dark:bg-slate-800 rounded-xl p-6 mb-4">
        <audio controls class="w-full">
            <source src="{{ $post->postable->audio_url }}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>
    @endif
    <div class="text-slate-700 dark:text-slate-300 leading-relaxed">
        {!! $post->content !!}
    </div>
</div>
