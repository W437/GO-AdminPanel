<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if primary key already exists before attempting to modify
        $primaryKey = DB::select("SHOW KEYS FROM restaurant_wallets WHERE Key_name = 'PRIMARY'");

        if (empty($primaryKey)) {
            // Only add primary key if it doesn't exist
            DB::statement('ALTER TABLE restaurant_wallets MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        }
        // If primary key already exists, skip - table is already correctly configured
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove auto increment and primary key
        DB::statement('ALTER TABLE restaurant_wallets DROP PRIMARY KEY');
        DB::statement('ALTER TABLE restaurant_wallets MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
    }
};
