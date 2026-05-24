<?php

use App\Actions\Fortify\CreateNewUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CreateNewUser', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->action = new CreateNewUser;
    });

    it('creates a user with Viewer role on registration', function () {
        $user = $this->action->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        expect($user->exists)->toBeTrue();
        expect($user->hasRole('Viewer'))->toBeTrue();
        expect($user->can('view posts'))->toBeTrue();
        expect($user->can('view tags'))->toBeTrue();
        expect($user->can('view categories'))->toBeTrue();
        expect($user->can('create posts'))->toBeFalse();
    });

    it('does not assign any other role besides Viewer', function () {
        $user = $this->action->create([
            'name' => 'Another User',
            'email' => 'another@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        expect($user->roles)->toHaveCount(1);
        expect($user->roles->first()->name)->toBe('Viewer');
    });
});
