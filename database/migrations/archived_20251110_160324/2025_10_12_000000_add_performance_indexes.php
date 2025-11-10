<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexes extends Migration
{
    /**
     * Run the migrations.
     * Adds critical indexes to improve query performance
     *
     * @return void
     */
    public function up()
    {
        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->index('user_id');
            }
            if (Schema::hasColumn('orders', 'restaurant_id')) {
                $table->index('restaurant_id');
            }
            if (Schema::hasColumn('orders', 'delivery_man_id')) {
                $table->index('delivery_man_id');
            }
            if (Schema::hasColumn('orders', 'order_status')) {
                $table->index('order_status');
            }
            if (Schema::hasColumn('orders', 'created_at')) {
                $table->index('created_at');
            }
        });

        // Food table indexes
        Schema::table('food', function (Blueprint $table) {
            if (Schema::hasColumn('food', 'restaurant_id')) {
                $table->index('restaurant_id');
            }
            if (Schema::hasColumn('food', 'category_id')) {
                $table->index('category_id');
            }
            if (Schema::hasColumn('food', 'status')) {
                $table->index('status');
            }
        });

        // Restaurants table indexes
        Schema::table('restaurants', function (Blueprint $table) {
            if (Schema::hasColumn('restaurants', 'zone_id')) {
                $table->index('zone_id');
            }
            if (Schema::hasColumn('restaurants', 'status')) {
                $table->index('status');
            }
            if (Schema::hasColumn('restaurants', 'active')) {
                $table->index('active');
            }
        });

        // Order Details table indexes
        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'order_id')) {
                $table->index('order_id');
            }
            if (Schema::hasColumn('order_details', 'food_id')) {
                $table->index('food_id');
            }
        });

        // Reviews table indexes
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'food_id')) {
                $table->index('food_id');
            }
            if (Schema::hasColumn('reviews', 'user_id')) {
                $table->index('user_id');
            }
            if (Schema::hasColumn('reviews', 'restaurant_id')) {
                $table->index('restaurant_id');
            }
        });

        // Notifications table indexes
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->index('user_id');
            }
            if (Schema::hasColumn('notifications', 'created_at')) {
                $table->index('created_at');
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['restaurant_id']);
            $table->dropIndex(['delivery_man_id']);
            $table->dropIndex(['order_status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('food', function (Blueprint $table) {
            $table->dropIndex(['restaurant_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropIndex(['zone_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['active']);
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['food_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['food_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['restaurant_id']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });
    }
}
