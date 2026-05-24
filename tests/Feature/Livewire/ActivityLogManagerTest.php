<?php

use App\Livewire\ActivityLogManager;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;

describe('ActivityLogManager Livewire Component', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    });

    it('renders successfully for admin users', function () {
        Livewire::actingAs($this->admin)
            ->test(ActivityLogManager::class)
            ->assertStatus(200);
    });

    it('displays activity log entries', function () {
        $subject = User::factory()->create(['name' => 'Test Subject']);

        Activity::create([
            'log_name' => 'default',
            'description' => 'Test Subject was created',
            'subject_type' => User::class,
            'subject_id' => $subject->id,
            'causer_type' => User::class,
            'causer_id' => $this->admin->id,
            'event' => 'created',
            'properties' => [],
        ]);

        Livewire::actingAs($this->admin)
            ->test(ActivityLogManager::class)
            ->assertSee('Test Subject was created')
            ->assertSee('Created')
            ->assertSee($this->admin->name);
    });

    it('filters by causer', function () {
        $actor = User::factory()->create(['name' => 'Actor User']);
        $other = User::factory()->create(['name' => 'Other User']);

        Activity::create([
            'log_name' => 'default',
            'description' => 'Actor did something',
            'subject_type' => User::class,
            'subject_id' => $actor->id,
            'causer_type' => User::class,
            'causer_id' => $actor->id,
            'event' => 'updated',
            'properties' => [],
        ]);

        Activity::create([
            'log_name' => 'default',
            'description' => 'Other did something',
            'subject_type' => User::class,
            'subject_id' => $other->id,
            'causer_type' => User::class,
            'causer_id' => $other->id,
            'event' => 'updated',
            'properties' => [],
        ]);

        Livewire::actingAs($this->admin)
            ->test(ActivityLogManager::class)
            ->set('causerId', $actor->id)
            ->assertSee('Actor did something')
            ->assertDontSee('Other did something');
    });

    it('filters by event type', function () {
        $user = User::factory()->create();

        Activity::create([
            'log_name' => 'default',
            'description' => 'User created',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'causer_type' => User::class,
            'causer_id' => $this->admin->id,
            'event' => 'created',
            'properties' => [],
        ]);

        Activity::create([
            'log_name' => 'default',
            'description' => 'User updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'causer_type' => User::class,
            'causer_id' => $this->admin->id,
            'event' => 'updated',
            'properties' => [],
        ]);

        Livewire::actingAs($this->admin)
            ->test(ActivityLogManager::class)
            ->set('event', 'updated')
            ->assertSee('User updated')
            ->assertDontSee('User created');
    });

    it('filters by subject type', function () {
        $user = User::factory()->create();

        Activity::create([
            'log_name' => 'default',
            'description' => 'User updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'causer_type' => User::class,
            'causer_id' => $this->admin->id,
            'event' => 'updated',
            'properties' => [],
        ]);

        Livewire::actingAs($this->admin)
            ->test(ActivityLogManager::class)
            ->set('subjectType', 'App\Models\User')
            ->assertSee('User updated');
    });
});
