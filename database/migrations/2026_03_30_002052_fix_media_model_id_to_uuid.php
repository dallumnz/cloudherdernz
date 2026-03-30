<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Revert media table back to bigint for posts
        DB::table('media')->delete();
        
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['model_id', 'model_type']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->unsignedBigInteger('model_id')->index();
            $table->string('model_type')->index();
        });
    }

    public function down(): void
    {
        DB::table('media')->delete();
        
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['model_id', 'model_type']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->uuid('model_id')->nullable()->index();
            $table->string('model_type')->index();
        });
    }
};
