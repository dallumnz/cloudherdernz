<?php

namespace Database\Seeders;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Seeder;

class NewsletterSubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create active confirmed subscribers
        NewsletterSubscriber::create([
            'email' => 'subscriber1@example.com',
            'name' => 'John Doe',
            'status' => 'active',
            'subscribed_at' => now()->subDays(30),
            'confirmed_at' => now()->subDays(29),
            'ip_address' => '192.168.1.1',
            'preferences' => ['frequency' => 'weekly'],
        ]);

        NewsletterSubscriber::create([
            'email' => 'subscriber2@example.com',
            'name' => 'Jane Smith',
            'status' => 'active',
            'subscribed_at' => now()->subDays(15),
            'confirmed_at' => now()->subDays(14),
            'ip_address' => '192.168.1.2',
            'preferences' => ['frequency' => 'monthly'],
        ]);

        // Create pending subscriber
        NewsletterSubscriber::create([
            'email' => 'pending@example.com',
            'name' => 'Pending User',
            'status' => 'pending',
            'subscribed_at' => now()->subDays(2),
            'confirmed_at' => null,
            'ip_address' => '192.168.1.3',
        ]);

        // Create unsubscribed subscriber
        NewsletterSubscriber::create([
            'email' => 'unsubscribed@example.com',
            'name' => 'Former Subscriber',
            'status' => 'unsubscribed',
            'subscribed_at' => now()->subDays(60),
            'confirmed_at' => now()->subDays(59),
            'unsubscribed_at' => now()->subDays(10),
            'ip_address' => '192.168.1.4',
        ]);

        // Create additional random subscribers
        $additionalSubscribers = [
            ['email' => 'alice@example.com', 'name' => 'Alice Johnson'],
            ['email' => 'bob@example.com', 'name' => 'Bob Williams'],
            ['email' => 'charlie@example.com', 'name' => 'Charlie Brown'],
            ['email' => 'diana@example.com', 'name' => 'Diana Prince'],
            ['email' => 'eve@example.com', 'name' => null],
        ];

        foreach ($additionalSubscribers as $index => $data) {
            NewsletterSubscriber::create([
                'email' => $data['email'],
                'name' => $data['name'],
                'status' => 'active',
                'subscribed_at' => now()->subDays(20 + $index),
                'confirmed_at' => now()->subDays(19 + $index),
                'ip_address' => '192.168.1.'.(5 + $index),
            ]);
        }
    }
}
