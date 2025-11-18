<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Truncate existing campaigns FIRST as they use old system
        DB::table('item_campaigns')->truncate();

        // Create campaign_food junction table for many-to-many relationship
        Schema::create('campaign_food', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('food_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('campaign_id')->references('id')->on('item_campaigns')->onDelete('cascade');
            $table->foreign('food_id')->references('id')->on('food')->onDelete('cascade');

            // Indexes for better performance
            $table->index('campaign_id');
            $table->index('food_id');

            // Prevent duplicate entries
            $table->unique(['campaign_id', 'food_id']);
        });

        // Modify item_campaigns table to remove food-specific columns
        Schema::table('item_campaigns', function (Blueprint $table) {
            // Add zone_id for zone-specific campaigns
            if (!Schema::hasColumn('item_campaigns', 'zone_id')) {
                $table->unsignedBigInteger('zone_id')->after('restaurant_id')->nullable();
                $table->index('zone_id');
            }

            // Drop food-specific columns that are now fetched from linked food items
            $columnsToCheck = [
                'price',
                'tax',
                'tax_type',
                'discount',
                'discount_type',
                'category_id',
                'category_ids',
                'variations',
                'add_ons',
                'attributes',
                'choice_options',
                'veg',
                'restaurant_id',
                'maximum_cart_quantity'
            ];

            $columnsToDrop = [];
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('item_campaigns', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop campaign_food junction table
        Schema::dropIfExists('campaign_food');

        // Restore item_campaigns columns (basic structure, data cannot be restored)
        Schema::table('item_campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('item_campaigns', 'zone_id')) {
                $table->dropColumn('zone_id');
            }

            // Restore dropped columns with default values
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('category_ids', 191)->nullable();
            $table->text('variations')->nullable();
            $table->string('add_ons', 191)->nullable();
            $table->string('attributes', 191)->nullable();
            $table->text('choice_options')->nullable();
            $table->decimal('price', 24, 2)->default(0);
            $table->decimal('tax', 24, 2)->default(0.00);
            $table->string('tax_type', 20)->default('percent');
            $table->decimal('discount', 24, 2)->default(0.00);
            $table->string('discount_type', 20)->default('percent');
            $table->tinyInteger('veg')->default(0);
            $table->integer('maximum_cart_quantity')->nullable();
        });
    }
};
