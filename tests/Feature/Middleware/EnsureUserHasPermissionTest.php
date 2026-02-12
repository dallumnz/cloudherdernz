<?php

use App\Http\Middleware\EnsureUserHasPermission;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('EnsureUserHasPermission Middleware', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->middleware = new EnsureUserHasPermission;
    });

    it('allows access when user has permission', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $request = Request::create('/test');
        $request->setUserResolver(fn () => $admin);

        $next = fn ($req) => new Response('OK');

        $response = $this->middleware->handle($request, $next, 'view posts');

        expect($response->getContent())->toBe('OK');
        expect($response->getStatusCode())->toBe(200);
    });

    it('denies access when user does not have permission', function () {
        $viewer = User::factory()->create();
        $viewer->assignRole('Viewer');

        $request = Request::create('/test');
        $request->setUserResolver(fn () => $viewer);

        $next = fn ($req) => new Response('OK');

        try {
            $this->middleware->handle($request, $next, 'create posts');
            // If we reach here, the test should fail
            expect(true)->toBeFalse('Expected HttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            expect($e->getStatusCode())->toBe(403);
        }
    });

    it('denies access when user is not authenticated', function () {
        $request = Request::create('/test');
        $request->setUserResolver(fn () => null);

        $next = fn ($req) => new Response('OK');

        try {
            $this->middleware->handle($request, $next, 'view posts');
            // If we reach here, the test should fail
            expect(true)->toBeFalse('Expected HttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            expect($e->getStatusCode())->toBe(403);
        }
    });

    it('can be used via route middleware', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'permission:delete posts'])
            ->get('/test-middleware-route', fn () => 'Success');

        $response = $this->actingAs($admin)->get('/test-middleware-route');

        expect($response->status())->toBe(200);
    });
});
