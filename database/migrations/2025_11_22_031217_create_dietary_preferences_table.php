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
        // Create dietary_preferences table
        Schema::create('dietary_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type', 50)->comment('diet|religious|allergy|other');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        // Create pivot table for food-dietary_preferences relationship
        Schema::create('dietary_preference_food', function (Blueprint $table) {
            $table->foreignId('food_id')->constrained()->onDelete('cascade');
            $table->foreignId('dietary_preference_id')->constrained()->onDelete('cascade');
            $table->primary(['food_id', 'dietary_preference_id'], 'dp_food_primary');
        });

        // Seed with predefined dietary preferences
        $preferences = [
            // Diet Types
            ['name' => 'Vegan', 'type' => 'diet'],
            ['name' => 'Pescatarian', 'type' => 'diet'],
            ['name' => 'Egg-Free', 'type' => 'diet'],
            ['name' => 'Dairy-Free', 'type' => 'diet'],
            ['name' => 'Sugar-Free', 'type' => 'diet'],
            ['name' => 'Low-Carb', 'type' => 'diet'],
            ['name' => 'Keto-Friendly', 'type' => 'diet'],
            ['name' => 'High-Protein', 'type' => 'diet'],

            // Religious / Ethical
            ['name' => 'Kosher', 'type' => 'religious'],
            ['name' => 'No Alcohol', 'type' => 'religious'],

            // Allergy-related (complementing existing allergy system)
            ['name' => 'Gluten-Free', 'type' => 'allergy'],
            ['name' => 'Nut-Free', 'type' => 'allergy'],
            ['name' => 'Lactose-Free', 'type' => 'allergy'],
            ['name' => 'Soy-Free', 'type' => 'allergy'],
            ['name' => 'Shellfish-Free', 'type' => 'allergy'],
            ['name' => 'Sesame-Free', 'type' => 'allergy'],

            // Other
            ['name' => 'Organic', 'type' => 'other'],
            ['name' => 'Healthy', 'type' => 'other'],
            ['name' => 'Light', 'type' => 'other'],
        ];

        foreach ($preferences as $pref) {
            DB::table('dietary_preferences')->insert([
                'name' => $pref['name'],
                'type' => $pref['type'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dietary_preference_food');
        Schema::dropIfExists('dietary_preferences');
    }
};
