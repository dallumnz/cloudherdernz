<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Delete posts that reference newsletter posts (they had UUIDs, now invalid)
        DB::table('posts')
            ->where('postable_type', 'App\Models\NewsletterPost')
            ->delete();
    }

    public function down(): void
    {
        // Cannot restore
    }
};
