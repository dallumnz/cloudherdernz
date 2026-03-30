<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Delete existing newsletter posts (test data)
        DB::table('newsletter_posts')->delete();
        
        // Drop foreign key constraints on posts table first
        Schema::table('posts', function (Blueprint $table) {
            // Get the constraint name
            $constraints = DB::select("SELECT conname FROM pg_constraint WHERE conrelid = 'posts'::regclass AND contype = 'f'");
            foreach ($constraints as $constraint) {
                if (str_contains($constraint->conname, 'postable')) {
                    $table->dropForeign($constraint->conname);
                }
            }
        });
        
        // Drop and recreate newsletter_posts with bigint
        Schema::dropIfExists('newsletter_posts');
        
        Schema::create('newsletter_posts', function (Blueprint $table) {
            $table->id();
            $table->string('template')->nullable();
            $table->json('subscriber_settings')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->integer('recipients_count')->nullable();
            $table->integer('opens_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->timestamps();

            $table->index(['is_sent', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_posts');
        
        Schema::create('newsletter_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('template')->nullable();
            $table->json('subscriber_settings')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->integer('recipients_count')->nullable();
            $table->integer('opens_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->timestamps();

            $table->index(['is_sent', 'sent_at']);
        });
    }
};
