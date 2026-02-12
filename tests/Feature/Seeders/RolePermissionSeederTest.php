<?php

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

describe('RolePermissionSeeder', function () {
    beforeEach(function () {
        $this->seeder = new RolePermissionSeeder;
    });

    it('creates all required permissions', function () {
        $this->seeder->run();

        $expectedPermissions = [
            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts',
            'view tags', 'create tags', 'edit tags', 'delete tags',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view users', 'create users', 'edit users', 'delete users',
            'view roles', 'create roles', 'edit roles', 'delete roles', 'assign roles',
        ];

        foreach ($expectedPermissions as $permission) {
            expect(Permission::where('name', $permission)->exists())->toBeTrue();
        }
    });

    it('creates all required roles', function () {
        $this->seeder->run();

        $expectedRoles = ['Admin', 'Editor', 'Author', 'Viewer'];

        foreach ($expectedRoles as $role) {
            expect(Role::where('name', $role)->exists())->toBeTrue();
        }
    });

    it('assigns all permissions to admin role', function () {
        $this->seeder->run();

        $adminRole = Role::findByName('Admin');
        $permissionCount = \Spatie\Permission\Models\Permission::count();

        expect($adminRole->permissions)->toHaveCount($permissionCount);
    });

    it('assigns correct permissions to editor role', function () {
        $this->seeder->run();

        $editorRole = Role::findByName('Editor');

        expect($editorRole->hasPermissionTo('view posts'))->toBeTrue();
        expect($editorRole->hasPermissionTo('create posts'))->toBeTrue();
        expect($editorRole->hasPermissionTo('edit posts'))->toBeTrue();
        expect($editorRole->hasPermissionTo('delete posts'))->toBeTrue();
        expect($editorRole->hasPermissionTo('publish posts'))->toBeTrue();
        expect($editorRole->hasPermissionTo('view users'))->toBeFalse();
        expect($editorRole->hasPermissionTo('view roles'))->toBeFalse();
    });

    it('assigns correct permissions to author role', function () {
        $this->seeder->run();

        $authorRole = Role::findByName('Author');

        expect($authorRole->hasPermissionTo('view posts'))->toBeTrue();
        expect($authorRole->hasPermissionTo('create posts'))->toBeTrue();
        expect($authorRole->hasPermissionTo('edit posts'))->toBeTrue();
        expect($authorRole->hasPermissionTo('delete posts'))->toBeFalse();
        expect($authorRole->hasPermissionTo('publish posts'))->toBeFalse();
    });

    it('assigns correct permissions to viewer role', function () {
        $this->seeder->run();

        $viewerRole = Role::findByName('Viewer');

        expect($viewerRole->hasPermissionTo('view posts'))->toBeTrue();
        expect($viewerRole->hasPermissionTo('create posts'))->toBeFalse();
        expect($viewerRole->hasPermissionTo('edit posts'))->toBeFalse();
        expect($viewerRole->hasPermissionTo('view tags'))->toBeTrue();
        expect($viewerRole->hasPermissionTo('view categories'))->toBeTrue();
    });

    it('is idempotent', function () {
        $this->seeder->run();
        $permissionCount = Permission::count();
        $roleCount = Role::count();

        $this->seeder->run();

        expect(Permission::count())->toBe($permissionCount);
        expect(Role::count())->toBe($roleCount);
    });
});
