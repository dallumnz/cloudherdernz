<?php

use App\Livewire\ApiTokenManager;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('ApiTokenManager', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
    });

    it('renders for authenticated user with create posts permission', function () {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        Livewire::actingAs($user)
            ->test(ApiTokenManager::class)
            ->assertStatus(200);
    });

    it('creates a sanctum token and displays the plain text value', function () {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        Livewire::actingAs($user)
            ->test(ApiTokenManager::class)
            ->set('name', 'test-token')
            ->call('create')
            ->assertHasNoErrors()
            ->assertSet('plainTextToken', fn ($value) => str_starts_with($value, '1|'));

        expect($user->tokens()->where('name', 'test-token')->exists())->toBeTrue();
    });

    it('revokes a token', function () {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $token = $user->createToken('revoke-me');

        Livewire::actingAs($user)
            ->test(ApiTokenManager::class)
            ->call('revoke', $token->accessToken->id)
            ->assertSet('message', "Token 'revoke-me' revoked.");

        expect($user->tokens()->where('name', 'revoke-me')->exists())->toBeFalse();
    });

    it('prevents token creation without permission', function () {
        $user = User::factory()->create();
        $user->assignRole('Viewer');

        Livewire::actingAs($user)
            ->test(ApiTokenManager::class)
            ->set('name', 'hacker-token')
            ->call('create')
            ->assertSet('message', 'You do not have permission to create API tokens.');

        expect($user->tokens()->count())->toBe(0);
    });
});
