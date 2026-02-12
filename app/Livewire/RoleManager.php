<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    public ?int $selectedRoleId = null;

    public array $selectedPermissions = [];

    public string $message = '';

    public string $messageType = '';

    public function selectRole(int $roleId): void
    {
        $this->selectedRoleId = $roleId;
        $role = Role::find($roleId);

        if ($role) {
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        }
    }

    public function updatePermissions(): void
    {
        if (! $this->selectedRoleId) {
            $this->setMessage('Please select a role first.', 'error');

            return;
        }

        $role = Role::find($this->selectedRoleId);

        if (! $role) {
            $this->setMessage('Role not found.', 'error');

            return;
        }

        // Check authorization
        if (! auth()->user()?->can('edit roles')) {
            $this->setMessage('You do not have permission to edit roles.', 'error');

            return;
        }

        $role->syncPermissions($this->selectedPermissions);
        $this->setMessage("Permissions updated for role: {$role->name}", 'success');
    }

    public function togglePermission(string $permissionName): void
    {
        if (in_array($permissionName, $this->selectedPermissions)) {
            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionName]);
        } else {
            $this->selectedPermissions[] = $permissionName;
        }
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function render(): View
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);

            return end($parts); // Get the resource name (posts, tags, etc.)
        });

        $selectedRole = $this->selectedRoleId ? Role::find($this->selectedRoleId) : null;

        return view('livewire.role-manager', [
            'roles' => $roles,
            'permissions' => $permissions,
            'selectedRole' => $selectedRole,
        ]);
    }
}
