<div class="prose-editorial">
    {!! $post->content_html !!}
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Apply dropcap to first paragraph
    const firstParagraph = document.querySelector('.prose-editorial p');
    if (firstParagraph) {
        firstParagraph.classList.add('dropcap');
    }
});
</script>
@endpush
@endonce
