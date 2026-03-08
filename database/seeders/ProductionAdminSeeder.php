<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure admin role exists
        $adminRole = Role::firstOrCreate(
            [
                'name' => 'Admin'
            ]
        );

        // Create/update admin user (lookup by email, create if not exists)
        $user = User::firstOrCreate(
            ['email' => 'dallum.brown@gmail.com'],
            [
                'name' => 'Dallum',
                'password' => Hash::make($this->getPassword()),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        $user->roles()->syncWithoutDetaching([$adminRole->id]);

        $this->command->info('Production admin created: dallum.brown@gmail.com');
        $this->command->warn('Remember to change the default password after first login!');
    }

    private function getPassword(): string
    {
        // Check if password was provided via env
        $envPassword = env('ADMIN_INITIAL_PASSWORD');
        
        if ($envPassword) {
            return $envPassword;
        }

        // Generate a random password and display it
        $password = bin2hex(random_bytes(8)); // 16 char random
        
        $this->command->newLine();
        $this->command->alert('GENERATED PASSWORD: ' . $password);
        $this->command->newLine();
        
        return $password;
    }
}
