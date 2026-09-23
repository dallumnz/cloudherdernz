<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audio_posts', function (Blueprint $table): void {
            $table->string('audio_url')->nullable()->change();
        });

        Schema::table('video_posts', function (Blueprint $table): void {
            $table->string('video_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('audio_posts', function (Blueprint $table): void {
            $table->string('audio_url')->nullable(false)->change();
        });

        Schema::table('video_posts', function (Blueprint $table): void {
            $table->string('video_url')->nullable(false)->change();
        });
    }
};
