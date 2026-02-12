<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterPost>
 */
class NewsletterPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'template' => fake()->optional()->randomElement(['default', 'minimal', 'promotional']),
            'subscriber_settings' => fake()->optional()->passthrough([
                'send_to_all' => true,
                'segments' => [],
                'exclude_unsubscribed' => true,
            ]),
            'is_sent' => false,
            'sent_at' => null,
            'recipients_count' => null,
            'opens_count' => 0,
            'clicks_count' => 0,
        ];
    }

    /**
     * Indicate that the newsletter has been sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sent' => true,
            'sent_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'recipients_count' => fake()->numberBetween(100, 10000),
        ]);
    }

    /**
     * Indicate that the newsletter has engagement metrics.
     */
    public function withEngagement(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sent' => true,
            'sent_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'recipients_count' => $recipients = fake()->numberBetween(100, 10000),
            'opens_count' => fake()->numberBetween(0, $recipients),
            'clicks_count' => fake()->numberBetween(0, $recipients),
        ]);
    }
}
