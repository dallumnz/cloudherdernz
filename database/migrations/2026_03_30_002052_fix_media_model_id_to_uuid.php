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
        // Drop the existing morph columns and recreate as uuidMorphs
        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['model_id', 'model_type']);
            $table->dropColumn(['model_id', 'model_type']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->uuid('model_id')->index();
            $table->string('model_type')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['model_id', 'model_type']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->morphs('model');
        });
    }
};
