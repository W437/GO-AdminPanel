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
        // Add auto increment and primary key to id column
        DB::statement('ALTER TABLE restaurant_wallets MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
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
