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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    {{-- About / Logo --}}
                    <div>
                        <h3 class="text-xl font-bold mb-4">
                            <span class="text-white">CloudHerder</span><span class="text-red-500">.nz</span>
                        </h3>
                        <p class="text-gray-400 text-sm mb-4">A modern CMS built with Laravel and Livewire.</p>
                        <h4 class="font-semibold mb-3">Follow Us</h4>
                        <div class="flex space-x-3">
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                            </a>
                            <a href="{{ route('feed') }}" class="text-gray-400 hover:text-white transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 18 4 6.18c0 1.36.72 2.56 1.82 3.18V11.2a2.18 2.18 0 0 1 2.18-2.18h1.18zM4 4a2.18 2.18 0 0 1 2.18 2.18A2.18 2.18 0 0 1 8.36 8.36 2.18 2.18 0 0 1 6.18 6.18C6.18 16.18 4 14 4 6.18 4zM4 14a2.18 2.18 0 0 1 2.18 2.18 2.18 2.18 0 0 1-2.18 2.18 2.18 2.18 0 0 1-2.18-2.18A2.18 2.18 0 0 1 4 14z"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    <div>
                        <h4 class="font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm transition">Home</a></li>
                            <li><a href="{{ route('posts.index') }}" class="text-gray-400 hover:text-white text-sm transition">Posts</a></li>
                            <li><a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-white text-sm transition">Categories</a></li>
                            <li><a href="{{ route('tags.index') }}" class="text-gray-400 hover:text-white text-sm transition">Tags</a></li>
                            <li><a href="{{ route('sitemap') }}" class="text-gray-400 hover:text-white text-sm transition">Sitemap</a></li>
                        </ul>
                    </div>

                    {{-- Company --}}
                    <div>
                        <h4 class="font-semibold mb-4">Company</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('contact.show') }}" class="text-gray-400 hover:text-white text-sm transition">Contact Us</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white text-sm transition">About</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white text-sm transition">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white text-sm transition">Terms of Use</a></li>
                        </ul>
                    </div>

                    {{-- Newsletter --}}
                    <div>
                        <h4 class="font-semibold mb-4">Stay Updated</h4>
                        <p class="text-gray-400 text-sm mb-4">Subscribe to our newsletter for the latest updates.</p>
                        <form class="flex">
                            <input 
                                type="email" 
                                placeholder="Your email" 
                                class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-l-lg text-white text-sm focus:outline-none focus:border-blue-500"
                            >
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-r-lg transition">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-10 pt-8 text-center text-gray-500 text-sm">
                    <p>&copy; {{ date('Y') }} CloudHerder.nz. All rights reserved.</p>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
