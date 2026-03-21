<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head-frontend')
        {{ $head ?? '' }}
    </head>
    <body class="min-h-screen bg-surface text-on-surface selection:bg-primary-fixed selection:text-on-surface">
        {{-- Public Navigation --}}
        <x-public-navigation />

        {{-- Main Content --}}
        <main>
            {{ $slot }}
        </main>

        {{-- Cookie Notice --}}
        <livewire:cookie-notice />

        {{-- Footer --}}
        <footer class="bg-surface-container-low w-full py-12 px-6 md:px-8 mt-12">
            <div class="max-w-screen-2xl mx-auto">
                {{-- Separator --}}
                <div class="bg-surface-container h-6 w-full mb-10 rounded"></div>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                    {{-- Logo --}}
                    <div class="flex flex-col gap-2">
                        <span class="font-headline font-bold text-xl text-on-surface letterpress">Cloud Herder</span>
                        <p class="font-body text-sm text-on-surface-variant max-w-xs">
                            Curated perspectives on technology, culture, and the spaces between.
                        </p>
                    </div>
                    
                    {{-- Links --}}
                    <nav class="flex flex-wrap gap-x-8 gap-y-3">
                        <a href="#" class="font-label text-sm text-on-surface-variant hover:text-primary transition-colors">Follow Us</a>
                        <a href="{{ route('contact.show') }}" class="font-label text-sm text-on-surface-variant hover:text-primary transition-colors">Contact</a>
                        <a href="{{ route('privacy') }}" class="font-label text-sm text-on-surface-variant hover:text-primary transition-colors">Privacy</a>
                    </nav>
                    
                    {{-- Social --}}
                    <div class="flex gap-4">
                        <a href="#" class="text-on-surface-variant hover:text-primary transition-colors" aria-label="RSS">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-on-surface-variant hover:text-primary transition-colors" aria-label="Twitter">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div class="border-t border-outline-variant/20 mt-8 pt-8 text-center">
                    <p class="font-label text-xs text-on-surface-variant/60">
                        © {{ date('Y') }} Cloud Herder. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
