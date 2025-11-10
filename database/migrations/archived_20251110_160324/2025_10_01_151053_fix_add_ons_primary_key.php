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
        // Add auto increment to id column (PRIMARY KEY already exists)
        DB::statement('ALTER TABLE add_ons MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove auto increment
        DB::statement('ALTER TABLE add_ons MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
    }
};
