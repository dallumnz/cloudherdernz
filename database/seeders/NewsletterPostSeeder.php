<?php

namespace Database\Seeders;

use App\Models\NewsletterPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsletterPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        // Create draft newsletters
        for ($i = 0; $i < 3; $i++) {
            $newsletterPost = NewsletterPost::factory()->create();
            Post::factory()->create([
                'author_id' => $user->id,
                'title' => 'Newsletter Draft '.($i + 1),
                'slug' => 'newsletter-draft-'.($i + 1),
                'status' => 'draft',
                'postable_type' => NewsletterPost::class,
                'postable_id' => $newsletterPost->id,
            ]);
        }

        // Create sent newsletters
        for ($i = 0; $i < 2; $i++) {
            $newsletterPost = NewsletterPost::factory()->sent()->withEngagement()->create();
            Post::factory()->create([
                'author_id' => $user->id,
                'title' => 'Monthly Newsletter '.($i + 1),
                'slug' => 'monthly-newsletter-'.($i + 1),
                'status' => 'published',
                'published_at' => $newsletterPost->sent_at,
                'postable_type' => NewsletterPost::class,
                'postable_id' => $newsletterPost->id,
            ]);
        }
    }
}
