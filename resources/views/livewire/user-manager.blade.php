<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">User Management</flux:heading>
        @can('create users')
            <flux:button wire:click="create" variant="primary" icon="plus">
                Create User
            </flux:button>
        @endcan
    </div>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}" wire:poll.5s="$set('message', '')">
            {{ $message }}
        </flux:callout>
    @endif

    {{-- Search --}}
    <div class="flex items-center space-x-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search users..."
            class="max-w-md"
        />
    </div>

    {{-- Form --}}
    @if ($showForm)
        <flux:card>
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit User' : 'Create User' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        wire:model="name"
                        label="Name"
                        placeholder="User name"
                        required
                    />
                    <flux:input
                        wire:model="email"
                        type="email"
                        label="Email"
                        placeholder="user@example.com"
                        required
                    />
                </div>

                <flux:input
                    wire:model="password"
                    type="password"
                    label="Password"
                    placeholder="{{ $editingId ? 'Leave empty to keep current password' : 'Minimum 8 characters' }}"
                />

                {{-- Roles --}}
                <div>
                    <flux:text variant="secondary" size="sm" class="mb-2">Roles</flux:text>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->roles as $role)
                            <label class="inline-flex items-center px-3 py-1 rounded-full text-sm cursor-pointer transition-colors {{ in_array($role->name, $selectedRoles) ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                <input
                                    type="checkbox"
                                    wire:model="selectedRoles"
                                    value="{{ $role->name }}"
                                    class="sr-only"
                                >
                                {{ $role->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center space-x-3 pt-4">
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </flux:button>
                    <flux:button type="button" wire:click="cancel" variant="ghost">
                        Cancel
                    </flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- Users List --}}
    <flux:card>
        @if ($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4 font-semibold">User</th>
                            <th class="py-3 px-4 font-semibold">Email</th>
                            <th class="py-3 px-4 font-semibold">Roles</th>
                            <th class="py-3 px-4 font-semibold">Joined</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-800" wire:key="user-{{ $user->id }}">
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-3">
                                        <flux:avatar :name="$user->name" :initials="$user->initials()" />
                                        <span class="font-medium">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($user->roles as $role)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-sm">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        @can('edit users')
                                            <flux:button wire:click="edit({{ $user->id }})" size="sm" variant="ghost">
                                                Edit
                                            </flux:button>
                                        @endcan
                                        @can('delete users')
                                            @if ($user->id !== auth()->id())
                                                <flux:button
                                                    wire:click="delete({{ $user->id }})"
                                                    wire:confirm="Are you sure you want to delete '{{ $user->name }}'?"
                                                    size="sm"
                                                    variant="danger"
                                                >
                                                    Delete
                                                </flux:button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <flux:icon name="users" class="w-12 h-12 mx-auto mb-4" />
                <p>No users found.</p>
                @if ($search)
                    <p class="text-sm mt-2">Try adjusting your search.</p>
                @endif
            </div>
        @endif
    </flux:card>
</div>
