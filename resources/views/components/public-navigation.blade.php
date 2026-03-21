<nav class="bg-surface w-full sticky top-0 z-50 border-b border-surface-container-low">
    <div class="flex justify-between items-center px-6 md:px-8 py-5 w-full max-w-screen-2xl mx-auto">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="font-headline font-bold text-2xl text-on-surface tracking-tighter letterpress">
            Cloud Herder
        </a>
        
        {{-- Navigation --}}
        <nav class="hidden md:flex items-center gap-8">
            <a href="{{ route('home') }}" 
               class="{{ request()->routeIs('home') ? 'text-primary border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary' }} font-headline italic text-lg tracking-tight transition-colors duration-300">
                Home
            </a>
            <a href="{{ route('posts.index') }}" 
               class="{{ request()->routeIs('posts.*') ? 'text-primary border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary' }} font-headline italic text-lg tracking-tight transition-colors duration-300">
                Blog
            </a>
            <a href="{{ route('contact.show') }}" 
               class="{{ request()->routeIs('contact.*') ? 'text-primary border-b-2 border-primary pb-1' : 'text-on-surface-variant hover:text-primary' }} font-headline italic text-lg tracking-tight transition-colors duration-300">
                Contact
            </a>
        </nav>
        
        {{-- Actions --}}
        <div class="flex items-center gap-4">
            {{-- Search --}}
            <a href="{{ route('search.index') }}" class="text-primary hover:text-primary-container transition-colors duration-300 p-2" aria-label="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </a>
            
            {{-- Theme Toggle --}}
            <button type="button" onclick="toggleTheme()" class="text-primary hover:text-primary-container transition-colors duration-300 p-2" aria-label="Toggle theme">
                <svg id="sun-icon" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg id="moon-icon" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            {{-- Mobile Menu --}}
            <button class="md:hidden text-primary hover:text-primary-container transition-colors duration-300 p-2" aria-label="Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- User Menu --}}
            @auth
                <a href="{{ route('dashboard') }}" class="hidden md:inline-flex items-center justify-center w-10 h-10 border border-outline-variant/30 rounded-lg text-primary hover:text-primary-container hover:border-primary/30 transition-all" aria-label="Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden md:inline-flex items-center justify-center w-10 h-10 border border-outline-variant/30 rounded-lg text-primary hover:text-primary-container hover:border-primary/30 transition-all" aria-label="Login">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>
            @endauth
        </div>
    </div>
</nav>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// Initialize theme from localStorage
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});
</script>
