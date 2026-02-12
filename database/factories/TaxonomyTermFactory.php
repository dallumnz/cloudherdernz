<?php

namespace Database\Factories;

use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaxonomyTerm>
 */
class TaxonomyTermFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'taxonomy_id' => Taxonomy::factory(),
            'parent_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'metadata' => null,
        ];
    }
}
