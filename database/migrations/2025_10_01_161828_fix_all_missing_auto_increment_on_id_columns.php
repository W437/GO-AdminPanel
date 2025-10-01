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
        // Fix caused by importing SQL seed data with explicit IDs
        // This removed AUTO_INCREMENT from 120+ tables

        // Get all tables with id column missing AUTO_INCREMENT
        $tables = DB::select("
            SELECT table_name
            FROM information_schema.columns
            WHERE table_schema = DATABASE()
                AND column_name = 'id'
                AND extra NOT LIKE '%auto_increment%'
                AND table_name NOT IN ('migrations', 'stories', 'story_media', 'story_views', 'restaurant_wallets', 'add_ons', 'taxables')
            ORDER BY table_name
        ");

        foreach ($tables as $table) {
            $tableName = $table->table_name;

            // Check if PRIMARY KEY exists
            $hasPrimaryKey = DB::select("
                SHOW KEYS FROM `{$tableName}` WHERE Key_name = 'PRIMARY' AND Column_name = 'id'
            ");

            if (!empty($hasPrimaryKey)) {
                // Has PRIMARY KEY, just add AUTO_INCREMENT
                DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
            } else {
                // No PRIMARY KEY, add both
                DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
            }

            echo "Fixed AUTO_INCREMENT on: {$tableName}\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot safely reverse this as it would break the database again
        // The AUTO_INCREMENT should remain
    }
};
