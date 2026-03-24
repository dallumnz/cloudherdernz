<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
use RalphJSmit\Laravel\SEO\Support\SEOData;

// Check for explicit SEO model from route first (avoids variable pollution from loops)
$seoModel = request()->route('post') ?? request()->route('page') ?? null;

// Debug: Check if we're on homepage and what route returns
$isHome = request()->is('/');
$routePost = request()->route('post');
@endphp

@if($isHome)
    {!! seo(new SEOData(
        title: config('app.name') . ' - Curated perspectives on technology, culture, and the spaces between',
        description: 'Cloud Herder is a blog exploring technology, AI, web development, and the intersection of digital tools with human creativity.',
        image: asset('images/og-default.png')
    )) !!}
@elseif($seoModel && is_object($seoModel))
    {!! seo()->for($seoModel) !!}
@else
    {!! seo() !!}
@endif

<!-- Debug: isHome={!! $isHome ? 'true' : 'false' !!}, routePost={!! json_encode($routePost) !!} -->

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

{{-- Preload Newsreader fonts --}}
<link rel="preload" href="/fonts/Newsreader-Variable.ttf" as="font" type="font/ttf" crossorigin>
<link rel="preload" href="/fonts/Newsreader-Italic-Variable.ttf" as="font" type="font/ttf" crossorigin>

@vite(['resources/css/frontend.css', 'resources/js/frontend.js'])

<script>
    // Initialize theme on page load
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>
