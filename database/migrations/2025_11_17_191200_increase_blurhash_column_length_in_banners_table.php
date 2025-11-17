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
        Schema::table('banners', function (Blueprint $table) {
            $table->string('image_blurhash', 100)->nullable()->change();
            $table->string('video_thumbnail_blurhash', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('image_blurhash', 50)->nullable()->change();
            $table->string('video_thumbnail_blurhash', 50)->nullable()->change();
        });
    }
};
