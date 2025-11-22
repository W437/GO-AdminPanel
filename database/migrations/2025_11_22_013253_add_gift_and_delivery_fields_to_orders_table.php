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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('leave_at_door')->default(false)->after('order_note');
            $table->boolean('is_gift')->default(false)->after('leave_at_door');
            $table->string('gift_recipient_name')->nullable()->after('is_gift');
            $table->string('gift_recipient_phone')->nullable()->after('gift_recipient_name');
            $table->text('gift_message')->nullable()->after('gift_recipient_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['leave_at_door', 'is_gift', 'gift_recipient_name', 'gift_recipient_phone', 'gift_message']);
        });
    }
};
