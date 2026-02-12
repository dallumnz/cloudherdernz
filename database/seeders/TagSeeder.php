<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the tag taxonomy
        $tagTaxonomy = Taxonomy::query()
            ->firstOrCreate(
                ['type' => 'tag'],
                [
                    'name' => 'Tags',
                    'slug' => 'tags',
                    'description' => 'Content tags for posts',
                    'is_hierarchical' => false,
                ]
            );

        // Sample tag data
        $tags = [
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'Posts about Laravel framework',
            ],
            [
                'name' => 'PHP',
                'slug' => 'php',
                'description' => 'PHP programming language',
            ],
            [
                'name' => 'JavaScript',
                'slug' => 'javascript',
                'description' => 'JavaScript and frontend development',
            ],
            [
                'name' => 'Cloud Hosting',
                'slug' => 'cloud-hosting',
                'description' => 'Cloud hosting and infrastructure',
            ],
            [
                'name' => 'DevOps',
                'slug' => 'devops',
                'description' => 'Development operations and CI/CD',
            ],
        ];

        foreach ($tags as $tagData) {
            TaxonomyTerm::query()
                ->firstOrCreate(
                    [
                        'taxonomy_id' => $tagTaxonomy->id,
                        'slug' => $tagData['slug'],
                    ],
                    [
                        'name' => $tagData['name'],
                        'description' => $tagData['description'],
                        'parent_id' => null,
                        'metadata' => null,
                    ]
                );
        }
    }
}
