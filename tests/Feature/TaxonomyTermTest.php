<?php

use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use App\Models\User;

describe('TaxonomyTerm Feature', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->taxonomy = Taxonomy::factory()->create();
    });

    it('can list taxonomy terms', function () {
        $terms = TaxonomyTerm::factory()->count(3)->create(['taxonomy_id' => $this->taxonomy->id]);

        $response = $this->actingAs($this->user)
            ->get(route('taxonomy-terms.index'));

        $response->assertStatus(200);
    });

    it('can create a taxonomy term', function () {
        $response = $this->actingAs($this->user)
            ->get(route('taxonomy-terms.create'));

        $response->assertStatus(200);
    });

    it('can show a taxonomy term', function () {
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $this->taxonomy->id]);

        $response = $this->actingAs($this->user)
            ->get(route('taxonomy-terms.show', $term));

        $response->assertStatus(200);
    });

    it('can edit a taxonomy term', function () {
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $this->taxonomy->id]);

        $response = $this->actingAs($this->user)
            ->get(route('taxonomy-terms.edit', $term));

        $response->assertStatus(200);
    });

    it('can delete a taxonomy term', function () {
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $this->taxonomy->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('taxonomy-terms.destroy', $term));

        $response->assertRedirect(route('taxonomy-terms.index'));
    });
});
