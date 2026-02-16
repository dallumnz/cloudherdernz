<?php
$categories = App\Models\TaxonomyTerm::query()
    ->whereHas('taxonomy', fn ($q) => $q->where('type', 'category'))
    ->withCount('posts')
    ->orderBy('name')
    ->take(10)
    ->get();
?>
<nav class="border-b border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
    <div class="container mx-auto px-4">
        {{-- Logo Row --}}
        <div class="flex items-center justify-between py-3">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">CloudHerder</span>
                <span class="text-red-500">.nz</span>
            </a>

            {{-- Today's Date --}}
            <div class="text-gray-500 dark:text-gray-400 text-sm">
                {{ now()->format('j F, Y') }}
            </div>
        </div>

        {{-- Navigation Menu --}}
        <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-800 py-2">
            {{-- Main Nav Links --}}
            <div class="flex items-center space-x-1">
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
                    Home
                </a>
                <a href="{{ route('posts.index') }}" class="px-4 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
                    Posts
                </a>
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="px-4 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition flex items-center">
                        Categories
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="absolute top-full left-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                        @foreach($categories->take(5) as $category)
                            <a href="{{ route('categories.show', $category) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                {{ $category->name }}
                            </a>
                        @endforeach
                        <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-t border-gray-200 dark:border-gray-700">
                            View All
                        </a>
                    </div>
                </div>
                <a href="{{ route('tags.index') }}" class="px-4 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
                    Tags
                </a>
                <a href="{{ route('contact.show') }}" class="px-4 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 font-medium transition">
                    Contact
                </a>
            </div>

            {{-- Search Bar --}}
            <div class="relative">
                <form action="{{ route('search.index') }}" method="GET" class="flex items-center">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search posts..." 
                        class="w-48 lg:w-64 px-4 py-2 pl-10 text-sm bg-gray-100 dark:bg-gray-800 border-0 rounded-lg text-gray-700 dark:text-gray-300 placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                    <svg class="w-4 h-4 absolute left-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>
            </div>

            {{-- Auth Links --}}
            <div class="flex items-center space-x-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition">
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
