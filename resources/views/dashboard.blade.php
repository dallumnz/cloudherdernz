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

        @can('view posts')
            @php
                $adminStats = [
                    'total_posts' => \App\Models\Post::count(),
                    'published_posts' => \App\Models\Post::published()->count(),
                    'draft_posts' => \App\Models\Post::draft()->count(),
                    'total_tags' => \App\Models\TaxonomyTerm::whereHas('taxonomy', fn ($q) => $q->where('type', 'tag'))->count(),
                    'total_categories' => \App\Models\TaxonomyTerm::whereHas('taxonomy', fn ($q) => $q->where('type', 'category'))->count(),
                    'total_subscribers' => \App\Models\NewsletterSubscriber::count(),
                    'active_subscribers' => \App\Models\NewsletterSubscriber::where('status', 'active')->count(),
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <flux:card>
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text variant="secondary" size="sm">Total Posts</flux:text>
                            <flux:heading size="2xl">{{ $adminStats['total_posts'] }}</flux:heading>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <flux:icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center space-x-4 text-sm">
                        <span class="text-green-600 dark:text-green-400">{{ $adminStats['published_posts'] }} published</span>
                        <span class="text-gray-400">|</span>
                        <span class="text-yellow-600 dark:text-yellow-400">{{ $adminStats['draft_posts'] }} drafts</span>
                    </div>
                </flux:card>

                <flux:card>
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text variant="secondary" size="sm">Taxonomy</flux:text>
                            <flux:heading size="2xl">{{ $adminStats['total_tags'] + $adminStats['total_categories'] }}</flux:heading>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <flux:icon name="tag" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center space-x-4 text-sm">
                        <span class="text-purple-600 dark:text-purple-400">{{ $adminStats['total_tags'] }} tags</span>
                        <span class="text-gray-400">|</span>
                        <span class="text-indigo-600 dark:text-indigo-400">{{ $adminStats['total_categories'] }} categories</span>
                    </div>
                </flux:card>

                <flux:card>
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text variant="secondary" size="sm">Newsletter</flux:text>
                            <flux:heading size="2xl">{{ $adminStats['active_subscribers'] }}</flux:heading>
                        </div>
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <flux:icon name="newspaper" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center space-x-4 text-sm">
                        <span class="text-orange-600 dark:text-orange-400">{{ $adminStats['active_subscribers'] }} active</span>
                        <span class="text-gray-400">|</span>
                        <span class="text-gray-500">{{ $adminStats['total_subscribers'] }} total</span>
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
        @endcan

        {{-- Quick Actions --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">Quick Actions</flux:heading>
            <div class="flex flex-wrap gap-3">
                @can('create posts')
                    <flux:button href="{{ route('admin.posts', ['create' => 1]) }}" variant="primary" icon="plus">
                        New Post
                    </flux:button>
                @endcan

                @can('view pages')
                    <flux:button href="{{ route('admin.pages') }}" variant="outline" icon="document">
                        Manage Pages
                    </flux:button>
                @endcan

                @can('view posts')
                    <flux:button href="{{ route('admin.posts') }}" variant="outline" icon="document-text">
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

                @can('edit roles')
                    <flux:button href="{{ route('roles.manage') }}" variant="outline" icon="shield-check">
                        Manage Roles
                    </flux:button>
                @endcan

                @can('view newsletter subscribers')
                    <flux:button href="{{ route('admin.newsletter-subscribers.index') }}" variant="outline" icon="newspaper">
                        Newsletter Subscribers
                    </flux:button>
                @endcan

                @can('view contacts')
                    <flux:button href="{{ route('admin.inbox.index') }}" variant="outline" icon="envelope">
                        Contact Inbox
                    </flux:button>
                @endcan

                @can('view analytics')
                    <flux:button href="{{ route('admin.analytics') }}" variant="outline" icon="chart-bar">
                        Analytics
                    </flux:button>
                @endcan
            </div>
        </flux:card>

        {{-- Recent Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Posts --}}
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    @can('view posts')
                        <flux:heading size="lg">Recent Posts</flux:heading>
                    @else
                        <flux:heading size="lg">Your Recent Posts</flux:heading>
                    @endcan
                    @can('view posts')
                        <flux:button href="{{ route('admin.posts') }}" size="sm" variant="ghost">
                            View All
                        </flux:button>
                    @endcan
                </div>

                @php
                    if (auth()->user()->can('view posts')) {
                        $recentPosts = \App\Models\Post::query()
                            ->with(['postable', 'author'])
                            ->latest()
                            ->take(5)
                            ->get();
                    } else {
                        $recentPosts = auth()->user()->posts()->latest()->take(5)->get();
                    }
                @endphp

                @if ($recentPosts->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentPosts as $post)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('admin.posts', ['editId' => $post->id]) }}" class="font-medium truncate hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ \Illuminate\Support\Str::limit($post->title, 70) }}
                                    </a>
                                    <div class="flex items-center space-x-2 text-sm text-gray-500 mt-1">
                                        @can('view posts')
                                            <span>{{ $post->postable?->getKey() ? class_basename($post->postable_type) . ' Post' : 'Post' }}</span>
                                            <span>•</span>
                                        @endcan
                                        <span class="capitalize">{{ $post->status }}</span>
                                        @can('view posts')
                                            <span>•</span>
                                            <span>{{ $post->author?->name ?? 'Unknown' }}</span>
                                        @else
                                            <span>•</span>
                                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                                        @endcan
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
                            <flux:button href="{{ route('admin.posts', ['create' => 1]) }}" size="sm" variant="primary" class="mt-3">
                                Create Your First Post
                            </flux:button>
                        @endcan
                    </div>
                @endif
            </flux:card>

            @can('view posts')
                @php
                    $recentUsers = \App\Models\User::query()
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp

                <flux:card>
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="lg">Recent Users</flux:heading>
                        @can('view users')
                            <flux:button href="{{ route('admin.users') }}" size="sm" variant="ghost">
                                View All
                            </flux:button>
                        @endcan
                    </div>

                    @if ($recentUsers->count() > 0)
                        <div class="space-y-3">
                            @foreach ($recentUsers as $user)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <flux:avatar :name="$user->name" :initials="$user->initials()" />
                                        <div>
                                            <p class="font-medium">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $user->roles->pluck('name')->join(', ') ?: 'No roles' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <flux:icon name="users" class="w-12 h-12 mx-auto mb-3" />
                            <p>No users yet.</p>
                        </div>
                    @endif
                </flux:card>
            @else
                {{-- System Info for non-admin users --}}
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
            @endcan
        </div>

        @can('view posts')
            {{-- System Info for admins (below the grid) --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">System Information</flux:heading>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
        @endcan
    </div>
</x-layouts::app>
