<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
use RalphJSmit\Laravel\SEO\Support\SEOData;

$seoModel = $post ?? $page ?? request()->route('post') ?? request()->route('page') ?? null;
@endphp

@if($seoModel)
    {!! seo()->for($seoModel) !!}
@elseif(request()->is('/'))
    {!! seo(new SEOData(title: config('app.name'))) !!}
@else
    {!! seo() !!}
@endif

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/frontend.css', 'resources/js/frontend.js'])

<script>
    // Initialize theme on page load
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Toggle theme function
    function toggleTheme() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
    }
</script>
