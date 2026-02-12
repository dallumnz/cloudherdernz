<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AudioPost>
 */
class AudioPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'audio_url' => fake()->url(),
            'duration_seconds' => fake()->numberBetween(60, 3600),
            'episode_number' => fake()->optional()->numberBetween(1, 100),
        ];
    }
}
