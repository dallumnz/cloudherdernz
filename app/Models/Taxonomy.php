<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxonomy extends Model
{
    /** @use HasFactory<\Database\Factories\TaxonomyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'is_hierarchical',
    ];

    protected function casts(): array
    {
        return [
            'is_hierarchical' => 'boolean',
        ];
    }

    public function terms(): HasMany
    {
        return $this->hasMany(TaxonomyTerm::class);
    }
}
