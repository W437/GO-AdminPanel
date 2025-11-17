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
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'tin')) {
                $table->string('tin', 255)->nullable()->after('additional_documents');
            }

            if (!Schema::hasColumn('restaurants', 'tin_expire_date')) {
                $table->date('tin_expire_date')->nullable()->after('tin');
            }

            if (!Schema::hasColumn('restaurants', 'tin_certificate_image')) {
                $table->string('tin_certificate_image', 255)->nullable()->after('tin_expire_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (Schema::hasColumn('restaurants', 'tin_certificate_image')) {
                $table->dropColumn('tin_certificate_image');
            }

            if (Schema::hasColumn('restaurants', 'tin_expire_date')) {
                $table->dropColumn('tin_expire_date');
            }

            if (Schema::hasColumn('restaurants', 'tin')) {
                $table->dropColumn('tin');
            }
        });
    }
};
