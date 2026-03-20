{{ $post->title }}
{{ str_repeat('=', strlen($post->title)) }}

@if($post->excerpt)
{{ $post->excerpt }}

@endif
{{ $content }}

---

View in Browser: {{ $viewInBrowserUrl }}

---

You're receiving this because you subscribed to CloudHerder.

Unsubscribe: {{ $unsubscribeUrl }}

© {{ date('Y') }} CloudHerder
