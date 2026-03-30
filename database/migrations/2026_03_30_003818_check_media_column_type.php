<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $result = DB::select("SELECT data_type FROM information_schema.columns WHERE table_name = 'media' AND column_name = 'model_id'");
        
        if (!empty($result) && $result[0]->data_type === 'uuid') {
            // Column is still UUID, we need to convert it to bigint
            // First, drop existing columns and recreate as bigint
            DB::statement('ALTER TABLE media DROP COLUMN IF EXISTS model_id');
            DB::statement('ALTER TABLE media DROP COLUMN IF EXISTS model_type');
            DB::statement('ALTER TABLE media ADD COLUMN model_id BIGINT');
            DB::statement('ALTER TABLE media ADD COLUMN model_type VARCHAR(255)');
            DB::statement('CREATE INDEX media_model_id_model_type_index ON media(model_id, model_type)');
        }
    }

    public function down(): void
    {
        // Not reversing this
    }
};
