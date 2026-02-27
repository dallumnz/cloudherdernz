<?php

namespace Database\Seeders;

use App\Enums\PostType;
use App\Models\Post;
use App\Models\StandardPost;
use App\Models\TaxonomyTerm;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first() ?? User::factory()->create(['name' => 'Dallum']);

        // Get existing terms
        $categoryTerms = TaxonomyTerm::whereHas('taxonomy', fn($q) => $q->where('slug', 'category'))->get()->keyBy('name');
        $tagTerms = TaxonomyTerm::whereHas('taxonomy', fn($q) => $q->where('slug', 'tag'))->get()->keyBy('slug');

        // Sample posts data
        $posts = [
            [
                'title' => 'Building a CMS with Laravel & Livewire',
                'slug' => 'building-cms-laravel-livewire',
                'excerpt' => 'A comprehensive guide to building your own content management system',
                'content' => "<h2>Introduction</h2><p>Building a CMS from scratch is a great way to learn Laravel. In this tutorial, we'll cover the essentials.</p><h2>Getting Started</h2><p>First, let's set up a new Laravel project and install Livewire.</p>",
                'category' => 'Tutorials',
                'tags' => ['laravel', 'tutorial', 'php'],
            ],
            [
                'title' => 'Setting up Local-First AI with Ollama',
                'slug' => 'setting-up-local-ai-ollama',
                'excerpt' => 'Run powerful AI models locally without depending on cloud services',
                'content' => "<h2>Why Local-First?</h2><p>Privacy, cost control, and no rate limits. What's not to love?</p><h2>Installing Ollama</h2><p>Ollama makes running AI models locally simple.</p>",
                'category' => 'Tutorials',
                'tags' => ['ai', 'self-hosted', 'local-first', 'tutorial'],
            ],
            [
                'title' => 'Self-Hosting Your Own AI Agents',
                'slug' => 'self-hosting-ai-agents',
                'excerpt' => 'Take control of your AI infrastructure',
                'content' => "<h2>The Case for Self-Hosting</h2><p>When you host your own agents, you own your data.</p>",
                'category' => 'Deep Dives',
                'tags' => ['ai', 'self-hosted', 'local-first', 'open-source'],
            ],
            [
                'title' => 'Creating a Design System with Tailwind',
                'slug' => 'design-system-tailwind',
                'excerpt' => 'Build consistent UIs with Tailwind CSS',
                'content' => "<h2>What is a Design System?</h2><p>A design system is a collection of reusable components.</p>",
                'category' => 'Tutorials',
                'tags' => ['tutorial', 'workflow', 'open-source'],
            ],
            [
                'title' => 'Migrating from WordPress to Laravel',
                'slug' => 'migrating-wordpress-laravel',
                'excerpt' => 'Making the switch from WP to something you control',
                'content' => "<h2>Why Leave WordPress?</h2><p>Sometimes you outgrow a platform.</p>",
                'category' => 'Tutorials',
                'tags' => ['laravel', 'tutorial', 'php', 'open-source'],
            ],
            [
                'title' => 'Why I Built CloudHerder',
                'slug' => 'why-i-built-cloudherder',
                'excerpt' => 'The story behind my Laravel-based CMS',
                'content' => "<h2>The Problem</h2><p>Existing CMS options didn't fit my workflow.</p><h2>The Solution</h2><p>Build something that does exactly what I need.</p>",
                'category' => 'News',
                'tags' => ['laravel', 'open-source', 'workflow'],
            ],
            [
                'title' => 'The Future of Local-First AI Development',
                'slug' => 'future-local-first-ai',
                'excerpt' => 'Where is this all heading?',
                'content' => "<h2>Trends to Watch</h2><p>Local models are getting better fast.</p>",
                'category' => 'Deep Dives',
                'tags' => ['ai', 'local-first', 'workflow'],
            ],
            [
                'title' => 'Why Static Sites Are Coming Back',
                'slug' => 'why-static-sites-coming-back',
                'excerpt' => 'The pendulum swings back to simplicity',
                'content' => "<h2>The JAMstack Revolution</h2><p>Static generation + client-side hydration = best of both worlds.</p>",
                'category' => 'Deep Dives',
                'tags' => ['open-source', 'workflow', 'php'],
            ],
            [
                'title' => 'How the Agent Agency Pattern Works',
                'slug' => 'agent-agency-pattern',
                'excerpt' => 'Orchestrating multiple AI agents for complex tasks',
                'content' => "<h2>What is an Agent Agency?</h2><p>Instead of one agent doing everything, coordinate multiple specialized agents.</p>",
                'category' => 'AI',
                'tags' => ['ai', 'workflow', 'local-first'],
            ],
            [
                'title' => 'Understanding the Software Factory Workflow',
                'slug' => 'software-factory-workflow',
                'excerpt' => 'Graph-based AI-driven development pipelines',
                'content' => "<h2>The Graph Approach</h2><p>Define your workflow as a DOT graph and let AI execute it.</p>",
                'category' => 'AI',
                'tags' => ['ai', 'workflow', 'local-first', 'open-source'],
            ],
        ];

        foreach ($posts as $postData) {
            $category = $postData['category'];
            $postTags = $postData['tags'];
            unset($postData['category'], $postData['tags']);

            // Create the postable first
            $postable = StandardPost::create();

            $post = Post::create(array_merge($postData, [
                'author_id' => $author->id,
                'status' => 'published',
                'published_at' => now(),
                'postable_type' => PostType::STANDARD->model(),
                'postable_id' => $postable->id,
            ]));

            // Attach category
            if (isset($categoryTerms[$category])) {
                $post->taxonomyTerms()->attach($categoryTerms[$category]->id);
            }

            // Attach tags
            foreach ($postTags as $tag) {
                if (isset($tagTerms[$tag])) {
                    $post->taxonomyTerms()->attach($tagTerms[$tag]->id);
                }
            }
        }

        $this->command->info('Created ' . count($posts) . ' posts with categories and tags!');
    }
}
