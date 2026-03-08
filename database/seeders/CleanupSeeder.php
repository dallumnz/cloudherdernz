<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\TaxonomyTerm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanupSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all posts (and their postable relations via cascade)
        Post::query()->delete();
        
        // Delete all taxonomy terms (categories and tags)
        TaxonomyTerm::query()->delete();
        
        // Delete standard post content (orphaned)
        DB::table('standard_posts')->delete();
        
        // Keep only the admin user with real email, delete sample users
        DB::table('users')
            ->whereIn('email', [
                'editor@example.com',
                'author@example.com', 
                'viewer@example.com'
            ])
            ->delete();

        $this->command->info('Cleaned up all sample content and users!');
    }
}
