<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Post permissions
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',

            // Tag permissions (taxonomy terms)
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',

            // Category permissions (taxonomy terms)
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role permissions
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',

            // Media permissions
            'view media',
            'upload media',
            'delete media',

            // Contact permissions
            'view contacts',
            'delete contacts',
            'manage contacts',

            // Page permissions
            'view pages',
            'create pages',
            'edit pages',
            'delete pages',

            // Analytics permissions
            'view analytics',

            // Newsletter subscriber permissions
            'view newsletter subscribers',
            'create newsletter subscribers',
            'edit newsletter subscribers',
            'delete newsletter subscribers',
            'export newsletter subscribers',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // Admin - has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Editor - can manage posts, tags, categories, pages but not users or roles
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $editorRole->givePermissionTo([
            // Post permissions
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            // Tag permissions
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            // Category permissions
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            // Page permissions
            'view pages',
            'create pages',
            'edit pages',
            'delete pages',
        ]);

        // Author - can create and edit own posts, view tags and categories
        $authorRole = Role::firstOrCreate(['name' => 'Author', 'guard_name' => 'web']);
        $authorRole->givePermissionTo([
            'view posts',
            'create posts',
            'edit posts',
            'view tags',
            'view categories',
        ]);

        // Viewer - can only view content
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewerRole->givePermissionTo([
            'view posts',
            'view tags',
            'view categories',
        ]);
    }
}
