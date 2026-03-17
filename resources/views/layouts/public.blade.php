<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head-frontend')
        {{ $head ?? '' }}
    </head>
    <body class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors">
        {{-- Public Navigation --}}
        <x-public-navigation />

        {{-- Main Content --}}
        <main>
            {{ $slot }}
        </main>

        {{-- Cookie Notice --}}
        <livewire:cookie-notice />

        {{-- Footer --}}
        <footer class="bg-slate-100 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 mt-12 transition-colors">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    {{-- Logo --}}
                    <div class="flex items-center space-x-2 mb-4 md:mb-0">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 bg-clip-text text-transparent">Cloud Herder</span>
                    </div>
                    
                    {{-- Links --}}
                    <nav class="flex items-center space-x-6">
                        <a href="#" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Follow Us</a>
                        <a href="{{ route('contact.show') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Contact</a>
                        <a href="{{ route('privacy') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Privacy</a>
                    </nav>
                </div>
                
                <div class="border-t border-slate-200 dark:border-slate-700 mt-6 pt-6 text-center">
                    <p class="text-slate-500 dark:text-slate-500 text-sm">© {{ date('Y') }} Cloud Herder. All rights reserved.</p>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
