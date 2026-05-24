<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $this->get(route('register'));

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('newly registered users are assigned the Viewer role', function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $this->get(route('register'));

    $response = $this->post(route('register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $user = \App\Models\User::where('email', 'jane@example.com')->first();

    expect($user->hasRole('Viewer'))->toBeTrue();
    expect($user->can('view posts'))->toBeTrue();
    expect($user->can('create posts'))->toBeFalse();
});