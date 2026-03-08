<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clean up any existing sample data first
        $this->call(CleanupSeeder::class);

        // Seed roles and permissions
        $this->call(RolePermissionSeeder::class);

        // Create production admin (you)
        $this->call(ProductionAdminSeeder::class);

        // Create empty taxonomies (categories and tags available but empty)
        $this->call([
            //TaxonomySeeder::class,
        ]);
    }
}
