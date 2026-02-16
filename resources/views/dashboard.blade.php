<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6">
        {{-- Welcome Section --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Welcome back, {{ auth()->user()->name }}!</flux:heading>
                <flux:text variant="secondary">Here's what's happening with your content.</flux:text>
            </div>
            <flux:text variant="secondary">{{ now()->format('F j, Y') }}</flux:text>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text variant="secondary" size="sm">Your Posts</flux:text>
                        <flux:heading size="2xl">{{ auth()->user()->posts()->count() }}</flux:heading>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <flux:icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text variant="secondary" size="sm">Published</flux:text>
                        <flux:heading size="2xl">{{ auth()->user()->posts()->published()->count() }}</flux:heading>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <flux:icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text variant="secondary" size="sm">Drafts</flux:text>
                        <flux:heading size="2xl">{{ auth()->user()->posts()->draft()->count() }}</flux:heading>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <flux:icon name="pencil" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text variant="secondary" size="sm">Your Role</flux:text>
                        <flux:heading size="2xl" class="text-lg">
                            {{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}
                        </flux:heading>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <flux:icon name="shield-check" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </flux:card>
        </div>

        {{-- Quick Actions --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">Quick Actions</flux:heading>
            <div class="flex flex-wrap gap-3">
                @can('view pages')
                    <flux:button href="{{ route('admin.pages') }}" variant="outline" icon="document">
                        Manage Pages
                    </flux:button>
                @endcan

                @can('create posts')
                    <flux:button href="{{ route('admin.posts') }}" variant="outline" icon="plus">
                        Manage Posts
                    </flux:button>
                @endcan

                @can('view tags')
                    <flux:button href="{{ route('admin.tags') }}" variant="outline" icon="tag">
                        Manage Tags
                    </flux:button>
                @endcan

                @can('view categories')
                    <flux:button href="{{ route('admin.categories') }}" variant="outline" icon="folder">
                        Manage Categories
                    </flux:button>
                @endcan
            </div>
        </flux:card>

        {{-- Recent Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Your Recent Posts --}}
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">Your Recent Posts</flux:heading>
                    @can('view posts')
                        <flux:button href="{{ route('admin.posts') }}" size="sm" variant="ghost">
                            View All
                        </flux:button>
                    @endcan
                </div>

                @php
                    $recentPosts = auth()->user()->posts()->latest()->take(5)->get();
                @endphp

                @if ($recentPosts->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentPosts as $post)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('admin.posts', ['editId' => $post->id]) }}" class="font-medium truncate hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $post->title }}
                                    </a>
                                    <div class="flex items-center space-x-2 text-sm text-gray-500 mt-1">
                                        <span class="capitalize">{{ $post->status }}</span>
                                        <span>•</span>
                                        <span>{{ $post->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    @if ($post->status === 'published')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <flux:icon name="document-text" class="w-12 h-12 mx-auto mb-3" />
                        <p>No posts yet.</p>
                        @can('create posts')
                            <flux:button href="{{ route('admin.posts') }}" size="sm" variant="primary" class="mt-3">
                                Create Your First Post
                            </flux:button>
                        @endcan
                    </div>
                @endif
            </flux:card>

            {{-- System Info --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">System Information</flux:heading>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <flux:icon name="cube" class="w-5 h-5 text-gray-500" />
                            <span class="font-medium">Laravel Version</span>
                        </div>
                        <span class="text-gray-600 dark:text-gray-400">{{ app()->version() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <flux:icon name="server" class="w-5 h-5 text-gray-500" />
                            <span class="font-medium">PHP Version</span>
                        </div>
                        <span class="text-gray-600 dark:text-gray-400">{{ phpversion() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <flux:icon name="server" class="w-5 h-5 text-gray-500" />
                            <span class="font-medium">Database</span>
                        </div>
                        <span class="text-gray-600 dark:text-gray-400">{{ config('database.default') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <flux:icon name="cog" class="w-5 h-5 text-gray-500" />
                            <span class="font-medium">Environment</span>
                        </div>
                        <span class="text-gray-600 dark:text-gray-400">{{ config('app.env') }}</span>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts::app>
