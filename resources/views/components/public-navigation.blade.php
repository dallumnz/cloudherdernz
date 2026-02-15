<nav class="border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">CloudHerder.nz</span>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
                    Home
                </a>
                <a href="{{ route('posts.index') }}" class="px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
                    Posts
                </a>

                {{-- Post Types Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition flex items-center">
                        Types
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="absolute top-full left-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        <a href="{{ route('posts.by-type', 'image') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Image Posts
                        </a>
                        <a href="{{ route('posts.by-type', 'video') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Video Posts
                        </a>
                        <a href="{{ route('posts.by-type', 'audio') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Audio Posts
                        </a>
                    </div>
                </div>

                <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
                    Categories
                </a>
                <a href="{{ route('tags.index') }}" class="px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
                    Tags
                </a>
                <a href="{{ route('search.index') }}" class="px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition" aria-label="Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </a>
            </div>

            {{-- Auth Links --}}
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
