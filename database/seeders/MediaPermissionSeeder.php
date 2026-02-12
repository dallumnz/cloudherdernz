<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MediaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create media-related permissions
        $permissions = [
            'view media',
            'upload media',
            'edit media',
            'delete media',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $adminRole = Role::findByName('Admin');
        $adminRole->givePermissionTo($permissions);

        $editorRole = Role::findByName('Editor');
        if ($editorRole) {
            $editorRole->givePermissionTo(['view media', 'upload media', 'edit media']);
        }

        $authorRole = Role::findByName('Author');
        if ($authorRole) {
            $authorRole->givePermissionTo(['view media', 'upload media']);
        }
    }
}
