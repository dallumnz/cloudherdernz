<?php

use App\Http\Middleware\EnsureUserHasPermission;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        } catch (HttpException $e) {
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
        } catch (HttpException $e) {
            expect($e->getStatusCode())->toBe(403);
        }
    });

    it('can be used via route middleware', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        Route::middleware(['web', 'auth', 'permission:delete posts'])
            ->get('/test-middleware-route', fn () => 'Success');

        $response = $this->actingAs($admin)->get('/test-middleware-route');

        expect($response->status())->toBe(200);
    });

    it('allows access via sanctum token when user has web permission', function () {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $token = $admin->createToken('api-test')->plainTextToken;

        Route::middleware(['auth:sanctum', 'permission:create posts'])
            ->post('/api/test-middleware-route', fn () => response()->json(['ok' => true]));

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/test-middleware-route');

        expect($response->status())->toBe(200)
            ->and($response->json('ok'))->toBeTrue();
    });

    it('denies access via sanctum token when user lacks web permission', function () {
        $viewer = User::factory()->create();
        $viewer->assignRole('Viewer');

        $token = $viewer->createToken('api-test')->plainTextToken;

        Route::middleware(['auth:sanctum', 'permission:create posts'])
            ->post('/api/test-middleware-route-denied', fn () => response()->json(['ok' => true]));

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/test-middleware-route-denied');

        expect($response->status())->toBe(403);
    });
});
