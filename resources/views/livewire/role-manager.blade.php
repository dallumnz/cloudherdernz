<div class="space-y-6">
    <flux:heading size="xl">Role Management</flux:heading>

    @if ($message)
        <flux:callout variant="{{ $messageType === 'success' ? 'success' : 'error' }}">
            {{ $message }}
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Roles List --}}
        <div class="lg:col-span-1">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Roles</flux:heading>
                <div class="space-y-2">
                    @foreach ($roles as $role)
                        <button
                            wire:click="selectRole({{ $role->id }})"
                            class="w-full text-left px-4 py-3 rounded-lg transition-colors {{ $selectedRoleId === $role->id ? 'bg-blue-100 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }} border"
                        >
                            <div class="font-semibold">{{ $role->name }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $role->permissions->count() }} permissions
                            </div>
                        </button>
                    @endforeach
                </div>
            </flux:card>
        </div>

        {{-- Permissions Editor --}}
        <div class="lg:col-span-2">
            <flux:card>
                @if ($selectedRole)
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="lg">Permissions for {{ $selectedRole->name }}</flux:heading>
                        <flux:button wire:click="updatePermissions" variant="primary">
                            Save Changes
                        </flux:button>
                    </div>

                    <div class="space-y-6">
                        @foreach ($permissions as $resource => $resourcePermissions)
                            <div>
                                <flux:heading size="md" class="mb-3 capitalize">{{ $resource }}</flux:heading>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($resourcePermissions as $permission)
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="selectedPermissions"
                                                value="{{ $permission->name }}"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            >
                                            <span class="text-sm">{{ ucfirst(str_replace(" {$resource}", '', $permission->name)) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <flux:icon name="shield-check" class="w-12 h-12 mx-auto mb-4" />
                        <p>Select a role to manage its permissions</p>
                    </div>
                @endif
            </flux:card>
        </div>
    </div>
</div>
