<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head-admin')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @auth
                    {{-- Content Management --}}
                    @canany(['view posts', 'create posts'])
                        <flux:sidebar.group :heading="__('Content')" class="grid">
                            @can('view pages')
                                <flux:sidebar.item icon="document" :href="route('admin.pages')" :current="request()->routeIs('admin.pages')" wire:navigate>
                                    {{ __('Pages') }}
                                </flux:sidebar.item>
                            @endcan
                            @can('view posts')
                                <flux:sidebar.item icon="document-text" :href="route('admin.posts')" :current="request()->routeIs('admin.posts')" wire:navigate>
                                    {{ __('Posts') }}
                                </flux:sidebar.item>
                            @endcan
                            @can('view tags')
                                <flux:sidebar.item icon="tag" :href="route('admin.tags')" :current="request()->routeIs('admin.tags')" wire:navigate>
                                    {{ __('Tags') }}
                                </flux:sidebar.item>
                            @endcan
                            @can('view categories')
                                <flux:sidebar.item icon="folder" :href="route('admin.categories')" :current="request()->routeIs('admin.categories')" wire:navigate>
                                    {{ __('Categories') }}
                                </flux:sidebar.item>
                            @endcan
                        </flux:sidebar.group>
                    @endcanany

                    {{-- Media --}}
                    @can('view media')
                        <flux:sidebar.group :heading="__('Media')" class="grid">
                            <flux:sidebar.item icon="photo" :href="route('admin.media.index')" :current="request()->routeIs('admin.media.*')" wire:navigate>
                                {{ __('Media Library') }}
                            </flux:sidebar.item>
                        </flux:sidebar.group>
                    @endcan
                    
                    {{-- Engagement and Feedback --}}
                    @canany(['view newsletter subscribers', 'view contacts'])
                        <flux:sidebar.group :heading="__('Engagement & Feedback')" class="grid">
                            @can('view analytics')
                                <flux:sidebar.item icon="chart-bar" :href="route('admin.analytics')" :current="request()->routeIs('admin.analytics')" wire:navigate>
                                    {{ __('Analytics') }}
                                </flux:sidebar.item>
                            @endcan
                            @can('view newsletter subscribers')
                                <flux:sidebar.item icon="newspaper" :href="route('admin.newsletter-subscribers.index')" :current="request()->routeIs('admin.newsletter-subscribers.index')" wire:navigate>
                                    {{ __('Newsletter Subscribers') }}
                                </flux:sidebar.item>
                            @endcan
                            @can('view contacts')
                                <flux:sidebar.item icon="envelope" :href="route('admin.inbox.index')" :current="request()->routeIs('admin.inbox.index')" wire:navigate>
                                    {{ __('Contact Inbox') }}
                                </flux:sidebar.item>
                            @endcan
                            @can('moderate comments')
                                <flux:sidebar.item icon="chat-bubble-left-right" :href="route('admin.comments')" :current="request()->routeIs('admin.comments')" wire:navigate>
                                    {{ __('Comments') }}
                                </flux:sidebar.item>
                            @endcan
                        </flux:sidebar.group>
                    @endcanany

                    {{-- User Management --}}
                    @canany(['view users', 'edit roles'])
                        <flux:sidebar.group :heading="__('Administration')" class="grid">
                            @can('view users')
                                <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                                    {{ __('Users') }}
                                </flux:sidebar.item>
                            @endcan
                            @can('edit roles')
                                <flux:sidebar.item icon="shield-check" :href="route('roles.manage')" :current="request()->routeIs('roles.manage')" wire:navigate>
                                    {{ __('Roles & Permissions') }}
                                </flux:sidebar.item>
                            @endcan
                        </flux:sidebar.group>
                    @endcanany
                @endauth
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/dallumnz/cloudherdernz" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://github.com/dallumnz/cloudherdernz" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
        @stack('scripts')
    </body>
</html>
