<?php

namespace Database\Factories;

use App\Enums\PostType;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\NewsletterPost;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(PostType::cases());

        return [
            'author_id' => User::factory(),
            'title' => fake()->sentence(),
            'slug' => Str::slug(fake()->sentence()),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(5, true),
            'markdown' => null,
            'metadata' => null,
            'status' => fake()->randomElement(['draft', 'published']),
            'published_at' => fake()->optional()->dateTime(),
            'postable_type' => $type->model(),
            'postable_id' => null, // Must be set explicitly
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'postable_type' => ImagePost::class,
        ]);
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'postable_type' => VideoPost::class,
        ]);
    }

    public function audio(): static
    {
        return $this->state(fn (array $attributes) => [
            'postable_type' => AudioPost::class,
        ]);
    }

    public function newsletter(): static
    {
        return $this->state(fn (array $attributes) => [
            'postable_type' => NewsletterPost::class,
        ]);
    }

    public function withMarkdown(): static
    {
        return $this->state(fn (array $attributes) => [
            'markdown' => '# '.fake()->sentence()."\n\n".fake()->paragraph()."\n\n## ".fake()->sentence()."\n\n".fake()->paragraph(),
            'content' => null,
        ]);
    }
}
