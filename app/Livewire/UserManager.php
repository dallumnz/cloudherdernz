<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public array $selectedRoles = [];

    public string $message = '';

    public string $messageType = 'success';

    public bool $showForm = false;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:8',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        // Check authorization
        if ($this->editingId) {
            if (! auth()->user()?->can('edit users')) {
                $this->setMessage('You do not have permission to edit users.', 'error');

                return;
            }
        } else {
            if (! auth()->user()?->can('create users')) {
                $this->setMessage('You do not have permission to create users.', 'error');

                return;
            }

            // Password is required for new users
            if (empty($this->password)) {
                $this->addError('password', 'Password is required for new users.');

                return;
            }
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (! empty($this->password)) {
            $data['password'] = bcrypt($this->password);
        }

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update($data);
            $user->syncRoles($this->selectedRoles);
            $this->setMessage("User '{$user->name}' updated successfully.", 'success');
        } else {
            $user = User::create($data);
            $user->syncRoles($this->selectedRoles);
            $this->setMessage("User '{$user->name}' created successfully.", 'success');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('user-saved');
    }

    public function delete(int $id): void
    {
        if (! auth()->user()?->can('delete users')) {
            $this->setMessage('You do not have permission to delete users.', 'error');

            return;
        }

        // Prevent self-deletion
        if ($id === auth()->id()) {
            $this->setMessage('You cannot delete your own account.', 'error');

            return;
        }

        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        $this->setMessage("User '{$name}' deleted successfully.", 'success');
        $this->dispatch('user-deleted');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRoles = [];
        $this->resetValidation();
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function getRolesProperty()
    {
        return Role::all();
    }

    public function render(): View
    {
        $query = User::query()->with('roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        /** @var LengthAwarePaginator $users */
        $users = $query->latest()->paginate(10);

        return view('livewire.user-manager', [
            'users' => $users,
        ]);
    }
}
