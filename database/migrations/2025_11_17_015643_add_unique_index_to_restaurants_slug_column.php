<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, ensure all existing restaurants have unique slugs
        $this->ensureUniqueSlugs();

        // Then add the unique constraint
        Schema::table('restaurants', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });
    }

    /**
     * Ensure all existing restaurants have unique slugs before adding constraint
     */
    private function ensureUniqueSlugs(): void
    {
        $restaurants = DB::table('restaurants')->get();
        $usedSlugs = [];

        foreach ($restaurants as $restaurant) {
            // Generate slug from name if null
            $baseSlug = $restaurant->slug ?: Str::slug($restaurant->name);

            // Ensure uniqueness by appending number if needed
            $slug = $baseSlug;
            $counter = 1;

            while (in_array($slug, $usedSlugs)) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $usedSlugs[] = $slug;

            // Update restaurant with unique slug
            DB::table('restaurants')
                ->where('id', $restaurant->id)
                ->update(['slug' => $slug]);
        }
    }
};
