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
        Schema::table('stories', function (Blueprint $table) {
            $table->json('overlays')->nullable()->after('title');
            $table->boolean('has_overlays')->default(false)->after('overlays');
            $table->string('type', 20)->nullable()->after('restaurant_id');
            $table->string('media_url', 2048)->nullable()->after('type');
            $table->string('thumbnail_url', 2048)->nullable()->after('media_url');
            $table->unsignedInteger('duration_seconds')->default(5)->after('thumbnail_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn([
                'overlays',
                'has_overlays',
                'type',
                'media_url',
                'thumbnail_url',
                'duration_seconds',
            ]);
        });
    }
};
