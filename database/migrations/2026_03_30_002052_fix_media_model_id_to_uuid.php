<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if model_id is still bigint and convert to uuid
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        
        if ($driver === 'pgsql') {
            // For PostgreSQL, check column type first
            $result = DB::selectOne("SELECT data_type FROM information_schema.columns WHERE table_name = 'media' AND column_name = 'model_id'");
            
            if ($result && in_array($result->data_type, ['bigint', 'integer'])) {
                // Clear media table since existing data has wrong type
                DB::table('media')->delete();
                
                // Drop existing columns and recreate as uuid
                Schema::table('media', function (Blueprint $table) {
                    $table->dropColumn(['model_id', 'model_type']);
                });

                Schema::table('media', function (Blueprint $table) {
                    $table->uuid('model_id')->nullable()->index();
                    $table->string('model_type')->index();
                });
            }
        } else {
            // MySQL/SQLite - just try to drop and recreate
            DB::table('media')->delete();
            
            Schema::table('media', function (Blueprint $table) {
                $table->dropColumn(['model_id', 'model_type']);
            });

            Schema::table('media', function (Blueprint $table) {
                $table->uuid('model_id')->nullable()->index();
                $table->string('model_type')->index();
            });
        }
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
