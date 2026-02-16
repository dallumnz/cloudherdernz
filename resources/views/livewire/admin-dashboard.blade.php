<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Admin Dashboard</flux:heading>
        <flux:text variant="secondary">{{ now()->format('F j, Y') }}</flux:text>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Posts Stats --}}
        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:text variant="secondary" size="sm">Total Posts</flux:text>
                    <flux:heading size="2xl">{{ $stats['total_posts'] }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <flux:icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="mt-4 flex items-center space-x-4 text-sm">
                <span class="text-green-600 dark:text-green-400">
                    {{ $stats['published_posts'] }} published
                </span>
                <span class="text-gray-400">|</span>
                <span class="text-yellow-600 dark:text-yellow-400">
                    {{ $stats['draft_posts'] }} drafts
                </span>
            </div>
        </flux:card>

        {{-- Users Stats --}}
        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:text variant="secondary" size="sm">Total Users</flux:text>
                    <flux:heading size="2xl">{{ $stats['total_users'] }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <flux:icon name="users" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="mt-4">
                <flux:text size="sm" variant="secondary">
                    {{ $roles->count() }} role{{ $roles->count() !== 1 ? 's' : '' }} configured
                </flux:text>
            </div>
        </flux:card>

        {{-- Taxonomy Stats --}}
        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:text variant="secondary" size="sm">Taxonomy</flux:text>
                    <flux:heading size="2xl">{{ $stats['total_tags'] + $stats['total_categories'] }}</flux:heading>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <flux:icon name="tag" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <div class="mt-4 flex items-center space-x-4 text-sm">
                <span class="text-purple-600 dark:text-purple-400">
                    {{ $stats['total_tags'] }} tags
                </span>
                <span class="text-gray-400">|</span>
                <span class="text-indigo-600 dark:text-indigo-400">
                    {{ $stats['total_categories'] }} categories
                </span>
            </div>
        </flux:card>

        {{-- Newsletter Stats --}}
        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:text variant="secondary" size="sm">Newsletter</flux:text>
                    <flux:heading size="2xl">{{ $stats['active_subscribers'] }}</flux:heading>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <flux:icon name="newspaper" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
            <div class="mt-4 flex items-center space-x-4 text-sm">
                <span class="text-orange-600 dark:text-orange-400">
                    {{ $stats['active_subscribers'] }} active
                </span>
                <span class="text-gray-400">|</span>
                <span class="text-gray-500">
                    {{ $stats['total_subscribers'] }} total
                </span>
            </div>
        </flux:card>
    </div>

    {{-- Quick Actions --}}
    <flux:card>
        <flux:heading size="lg" class="mb-4">Quick Actions</flux:heading>
        <div class="flex flex-wrap gap-3">
            @can('create posts')
                <flux:button href="{{ route('admin.posts', ['create' => 1]) }}" variant="primary" icon="plus">
                    New Post
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

            @can('view pages')
                <flux:button href="{{ route('admin.pages') }}" variant="outline" icon="document">
                    Manage Pages
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
                <flux:heading size="lg">Recent Posts</flux:heading>
                <flux:button href="{{ route('posts.index') }}" size="sm" variant="ghost">
                    View All
                </flux:button>
            </div>

            @if ($recentPosts->count() > 0)
                <div class="space-y-3">
                    @foreach ($recentPosts as $post)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('admin.posts', ['editId' => $post->id]) }}" class="font-medium truncate hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $post->title }}
                                </a>
                                <div class="flex items-center space-x-2 text-sm text-gray-500 mt-1">
                                    <span>{{ $post->postable?->getKey() ? class_basename($post->postable_type) . ' Post' : 'Post' }}</span>
                                    <span>•</span>
                                    <span class="capitalize">{{ $post->status }}</span>
                                    <span>•</span>
                                    <span>{{ $post->author?->name ?? 'Unknown' }}</span>
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
                    <flux:icon name="document" class="w-12 h-12 mx-auto mb-3" />
                    <p>No posts yet.</p>
                    @can('create posts')
                        <flux:button href="{{ route('admin.posts', ['create' => 1]) }}" size="sm" variant="primary" class="mt-3">
                            Create First Post
                        </flux:button>
                    @endcan
                </div>
            @endif
        </flux:card>

        {{-- Recent Users --}}
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
    </div>
</div>
