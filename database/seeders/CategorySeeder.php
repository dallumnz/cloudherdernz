<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the category taxonomy
        $categoryTaxonomy = Taxonomy::query()
            ->firstOrCreate(
                ['type' => 'category'],
                [
                    'name' => 'Categories',
                    'slug' => 'categories',
                    'description' => 'Content categories for posts',
                    'is_hierarchical' => true,
                ]
            );

        // Sample category data with hierarchy
        $categories = [
            [
                'name' => 'Development',
                'slug' => 'development',
                'description' => 'Software development topics',
                'children' => [
                    [
                        'name' => 'Backend',
                        'slug' => 'backend',
                        'description' => 'Backend development',
                    ],
                    [
                        'name' => 'Frontend',
                        'slug' => 'frontend',
                        'description' => 'Frontend development',
                    ],
                ],
            ],
            [
                'name' => 'Infrastructure',
                'slug' => 'infrastructure',
                'description' => 'Infrastructure and DevOps',
                'children' => [
                    [
                        'name' => 'Cloud',
                        'slug' => 'cloud',
                        'description' => 'Cloud computing',
                    ],
                    [
                        'name' => 'Servers',
                        'slug' => 'servers',
                        'description' => 'Server management',
                    ],
                ],
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business and management',
                'children' => [
                    [
                        'name' => 'Startups',
                        'slug' => 'startups',
                        'description' => 'Startup advice',
                    ],
                    [
                        'name' => 'Marketing',
                        'slug' => 'marketing',
                        'description' => 'Marketing strategies',
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parentCategory = TaxonomyTerm::query()
                ->firstOrCreate(
                    [
                        'taxonomy_id' => $categoryTaxonomy->id,
                        'slug' => $categoryData['slug'],
                    ],
                    array_merge($categoryData, [
                        'parent_id' => null,
                        'metadata' => null,
                    ])
                );

            // Create children
            foreach ($children as $childData) {
                TaxonomyTerm::query()
                    ->firstOrCreate(
                        [
                            'taxonomy_id' => $categoryTaxonomy->id,
                            'slug' => $childData['slug'],
                        ],
                        array_merge($childData, [
                            'parent_id' => $parentCategory->id,
                            'metadata' => null,
                        ])
                    );
            }
        }
    }
}
