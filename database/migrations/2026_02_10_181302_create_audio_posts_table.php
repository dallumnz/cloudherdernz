<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audio_posts', function (Blueprint $table) {
            $table->id();
            $table->string('audio_url');
            $table->integer('duration_seconds')->nullable();
            $table->integer('episode_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audio_posts');
    }
};
