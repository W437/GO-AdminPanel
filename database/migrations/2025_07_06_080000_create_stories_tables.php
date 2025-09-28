<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120)->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'status', 'publish_at']);
            $table->index('expire_at');
        });

        Schema::create('story_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('story_id')->constrained('stories')->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->string('media_type', 20);
            $table->string('media_path', 2048);
            $table->string('thumbnail_path', 2048)->nullable();
            $table->unsignedInteger('duration_seconds')->default(5);
            $table->string('caption', 240)->nullable();
            $table->string('cta_label', 120)->nullable();
            $table->string('cta_url', 2048)->nullable();
            $table->timestamps();

            $table->unique(['story_id', 'sequence']);
            $table->index('media_type');
        });

        Schema::create('story_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('story_id')->constrained('stories')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_key', 191)->nullable();
            $table->string('viewer_key', 191);
            $table->timestamp('viewed_at');
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->unique(['story_id', 'viewer_key']);
            $table->index(['story_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_views');
        Schema::dropIfExists('story_media');
        Schema::dropIfExists('stories');
    }
};
