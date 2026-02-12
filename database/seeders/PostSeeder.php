<?php

namespace Database\Seeders;

use App\Enums\PostType;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\Post;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@example.com')->first();

        if (! $user) {
            return;
        }

        // Create an Image Post
        $imagePost = ImagePost::create([
            'caption' => 'A beautiful sunset over the mountains',
            'gallery_settings' => json_encode(['layout' => 'grid', 'columns' => 3]),
        ]);

        $imagePost->posts()->create([
            'author_id' => $user->id,
            'title' => 'Beautiful Mountain Sunset',
            'slug' => 'beautiful-mountain-sunset',
            'excerpt' => 'Capturing the golden hour at Mount Cook',
            'content' => 'Today we witnessed an absolutely stunning sunset...',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Create a Video Post
        $videoPost = VideoPost::create([
            'video_url' => 'https://youtube.com/watch?v=dQw4w9WgXcQ',
            'thumbnail_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
            'duration_seconds' => 215,
            'provider' => 'youtube',
            'episode_number' => 1,
        ]);

        $videoPost->posts()->create([
            'author_id' => $user->id,
            'title' => 'Laravel Tutorial: Getting Started',
            'slug' => 'laravel-tutorial-getting-started',
            'excerpt' => 'Learn the basics of Laravel framework',
            'content' => 'In this tutorial, we will cover the fundamentals...',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Create an Audio Post
        $audioPost = AudioPost::create([
            'audio_url' => 'https://anchor.fm/s/12345678/podcast/play/12345678/audio.mp3',
            'duration_seconds' => 1800,
            'episode_number' => 42,
        ]);

        $audioPost->posts()->create([
            'author_id' => $user->id,
            'title' => 'Podcast Episode 42: The Future of AI',
            'slug' => 'podcast-episode-42-future-of-ai',
            'excerpt' => 'Discussing the implications of AGI',
            'content' => 'Welcome to episode 42 of our podcast...',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Create a few draft posts
        foreach (range(1, 3) as $i) {
            $type = fake()->randomElement(PostType::cases());

            $postable = match ($type) {
                PostType::IMAGE => ImagePost::factory()->create(),
                PostType::VIDEO => VideoPost::factory()->create(),
                PostType::AUDIO => AudioPost::factory()->create(),
            };

            $postable->posts()->create([
                'author_id' => $user->id,
                'title' => fake()->sentence(),
                'slug' => fake()->slug(),
                'excerpt' => fake()->paragraph(),
                'content' => fake()->paragraphs(3, true),
                'status' => 'draft',
            ]);
        }
    }
}
