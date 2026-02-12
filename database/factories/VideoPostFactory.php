<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VideoPost>
 */
class VideoPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'video_url' => fake()->url(),
            'thumbnail_url' => fake()->optional()->url(),
            'duration_seconds' => fake()->numberBetween(60, 3600),
            'provider' => fake()->randomElement(['youtube', 'vimeo', 'self']),
            'episode_number' => fake()->optional()->numberBetween(1, 100),
        ];
    }
}
