<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConverstionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'user_id')) {
                $table->renameColumn('user_id','sender_id');
            }
            if (!Schema::hasColumn('conversations', 'sender_type')) {
                $table->string('sender_type');
            }
            if (!Schema::hasColumn('conversations', 'receiver_id')) {
                $table->foreignId('receiver_id');
            }
            if (!Schema::hasColumn('conversations', 'receiver_type')) {
                $table->string('receiver_type');
            }
            if (!Schema::hasColumn('conversations', 'last_message_id')) {
                $table->foreignId('last_message_id')->nullable();
            }
            if (!Schema::hasColumn('conversations', 'last_message_time')) {
                $table->timestamp('last_message_time')->nullable();
            }
            if (!Schema::hasColumn('conversations', 'unread_message_count')) {
                $table->integer('unread_message_count')->default(0);
            }
            if (Schema::hasColumn('conversations', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('conversations', 'reply')) {
                $table->dropColumn('reply');
            }
            if (Schema::hasColumn('conversations', 'checked')) {
                $table->dropColumn('checked');
            }
            if (Schema::hasColumn('conversations', 'image')) {
                $table->dropColumn('image');
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
        Schema::table('conversations', function (Blueprint $table) {
            $table->renameColumn('sender_id', 'user_id');
            $table->string('message');
            $table->dropColumn('receiver_id');
            $table->dropColumn('receiver_type');
            $table->dropColumn('last_message_id')->nullable();
            $table->dropColumn('last_message_time')->nullable();
            $table->dropColumn('sender_type');
            $table->string('reply');
            $table->boolean('checked');
            $table->string('image')->nullable();
        });
    }
}
