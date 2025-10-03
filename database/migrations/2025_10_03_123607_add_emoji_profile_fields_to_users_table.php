<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_emoji', 10)->nullable()->default('😊')->after('image')->comment('Emoji character for profile picture');
            $table->string('profile_bg_color', 7)->nullable()->default('#FF9800')->after('profile_emoji')->comment('Hex color code for emoji background');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_emoji', 'profile_bg_color']);
        });
    }
};
