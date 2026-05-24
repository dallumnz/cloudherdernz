<?php

use App\Livewire\UserManager;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Livewire\Livewire;

describe('UserManager Livewire Component', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    });

    it('renders successfully for admin users', function () {
        Livewire::actingAs($this->admin)
            ->test(UserManager::class)
            ->assertStatus(200);
    });

    it('assigns Viewer role by default when admin creates a user without selecting roles', function () {
        Livewire::actingAs($this->admin)
            ->test(UserManager::class)
            ->set('name', 'Test User')
            ->set('email', 'testuser@example.com')
            ->set('password', 'password123')
            ->set('selectedRoles', [])
            ->call('save')
            ->assertDispatched('user-saved');

        $user = User::where('email', 'testuser@example.com')->first();

        expect($user)->not->toBeNull();
        expect($user->hasRole('Viewer'))->toBeTrue();
        expect($user->can('view posts'))->toBeTrue();
        expect($user->can('create posts'))->toBeFalse();
    });

    it('allows admin to override default Viewer role when creating a user', function () {
        Livewire::actingAs($this->admin)
            ->test(UserManager::class)
            ->set('name', 'Editor User')
            ->set('email', 'editoruser@example.com')
            ->set('password', 'password123')
            ->set('selectedRoles', ['Editor'])
            ->call('save')
            ->assertDispatched('user-saved');

        $user = User::where('email', 'editoruser@example.com')->first();

        expect($user)->not->toBeNull();
        expect($user->hasRole('Editor'))->toBeTrue();
        expect($user->hasRole('Viewer'))->toBeFalse();
        expect($user->can('create posts'))->toBeTrue();
    });

    it('does not change roles on update if admin edits a user', function () {
        $user = User::factory()->create();
        $user->assignRole('Viewer');

        Livewire::actingAs($this->admin)
            ->test(UserManager::class)
            ->call('edit', $user->id)
            ->set('name', 'Updated Name')
            ->set('selectedRoles', ['Author'])
            ->call('save')
            ->assertDispatched('user-saved');

        $user->refresh();

        expect($user->hasRole('Author'))->toBeTrue();
        expect($user->hasRole('Viewer'))->toBeFalse();
    });
});
