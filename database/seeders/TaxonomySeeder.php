<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        // Create tag taxonomy (flat, non-hierarchical)
        Taxonomy::firstOrCreate(
            ['type' => 'tag'],
            [
                'name' => 'Tags',
                'slug' => 'tags',
                'description' => 'Content tags for posts',
                'is_hierarchical' => false,
            ]
        );

        // Create category taxonomy (hierarchical)
        Taxonomy::firstOrCreate(
            ['type' => 'category'],
            [
                'name' => 'Categories',
                'slug' => 'categories',
                'description' => 'Content categories for posts',
                'is_hierarchical' => true,
            ]
        );

        $this->command->info('Taxonomies created: Tags (flat) and Categories (hierarchical)');
    }
}
