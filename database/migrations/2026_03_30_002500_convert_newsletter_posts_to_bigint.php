<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Delete existing newsletter posts and activities (test data)
        DB::table('newsletter_activities')->delete();
        DB::table('newsletter_posts')->delete();
        
        // Drop foreign key constraints
        Schema::table('newsletter_activities', function (Blueprint $table) {
            $table->dropForeign(['newsletter_post_id']);
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
        
        // Update newsletter_activities to use bigint (raw SQL for PostgreSQL cast)
        DB::statement('ALTER TABLE newsletter_activities ALTER COLUMN newsletter_post_id TYPE bigint USING NULL');
        
        Schema::table('newsletter_activities', function (Blueprint $table) {
            $table->foreign('newsletter_post_id')->references('id')->on('newsletter_posts')->onDelete('cascade');
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
