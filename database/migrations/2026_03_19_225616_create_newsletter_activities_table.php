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
        Schema::create('newsletter_activities', function (Blueprint $table) {
            $table->id();
            $table->uuid('newsletter_post_id');
            $table->foreign('newsletter_post_id')->references('id')->on('newsletter_posts')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->string('status')->default('draft'); // draft, queued, sending, sent, failed, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->unsignedInteger('recipients_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->string('mailtrap_batch_id')->nullable();
            $table->text('error_message')->nullable();
            $table->json('test_recipients')->nullable();
            $table->boolean('is_test')->default(false);
            $table->timestamps();

            $table->index('status');
            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_activities');
    }
};
