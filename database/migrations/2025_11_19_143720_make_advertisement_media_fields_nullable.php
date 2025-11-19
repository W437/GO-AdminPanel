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
        Schema::table('advertisements', function (Blueprint $table) {
            // Ensure media fields are nullable (they already are, but this makes it explicit)
            // Set default values for rating and review fields since they're no longer used
            $table->boolean('is_rating_active')->default(false)->change();
            $table->boolean('is_review_active')->default(false)->change();

            // Ensure add_type defaults to restaurant_promotion
            $table->enum('add_type',['video_promotion','restaurant_promotion'])->default('restaurant_promotion')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - these are safe defaults
    }
};
