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
        Schema::table('advertisements', function (Blueprint $table) {
            // Set default values for rating and review fields since they're no longer used
            $table->boolean('is_rating_active')->default(false)->change();
            $table->boolean('is_review_active')->default(false)->change();
        });

        // Update existing records to set default add_type to restaurant_promotion where null
        DB::statement("UPDATE advertisements SET add_type = 'restaurant_promotion' WHERE add_type IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - these are safe defaults
    }
};
