<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        {{ $head ?? '' }}
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        {{-- Public Navigation --}}
        <x-public-navigation />

        {{-- Main Content --}}
        <main>
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="bg-gray-900 text-white py-12 mt-16">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">CloudHerder.nz</h3>
                        <p class="text-gray-400">A modern CMS built with Laravel and Livewire.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white">Home</a></li>
                            <li><a href="{{ route('posts.index') }}" class="text-gray-400 hover:text-white">Posts</a></li>
                            <li><a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-white">Categories</a></li>
                            <li><a href="{{ route('sitemap') }}" class="text-gray-400 hover:text-white">Sitemap</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Subscribe</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('feed') }}" class="text-gray-400 hover:text-white flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 18 4 6.18c0 1.36.72 2.56 1.82 3.18V11.2a2.18 2.18 0 0 1 2.18-2.18h1.18zM4 4a2.18 2.18 0 0 1 2.18 2.18A2.18 2.18 0 0 1 8.36 8.36 2.18 2.18 0 0 1 6.18 6.18C6.18 16.18 4 14 4 6.18 4zM4 14a2.18 2.18 0 0 1 2.18 2.18 2.18 2.18 0 0 1-2.18 2.18 2.18 2.18 0 0 1-2.18-2.18A2.18 2.18 0 0 1 4 14z"/><circle cx="6.18" cy="17.64" r="2.18"/><circle cx="17.82" cy="17.64" r="2.18"/></svg>
                                RSS Feed
                            </a></li>
                            <li><a href="{{ route('contact.show') }}" class="text-gray-400 hover:text-white">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} CloudHerder.nz. All rights reserved.</p>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
