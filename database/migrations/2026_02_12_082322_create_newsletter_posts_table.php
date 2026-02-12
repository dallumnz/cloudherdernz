<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_posts');
    }
};
