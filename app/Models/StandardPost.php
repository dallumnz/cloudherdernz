<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandardPost extends Model
{
    protected $table = 'standard_posts';
    
    public function post(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Post::class, 'postable');
    }
}
