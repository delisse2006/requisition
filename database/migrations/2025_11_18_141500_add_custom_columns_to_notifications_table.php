<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add user_id to reference a specific user (optional)
            if (! Schema::hasColumn('notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->index();
            }

            // Add human-friendly title/message columns used by the app
            if (! Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable();
            }

            if (! Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable();
            }

            // Add a boolean read flag for quicker queries (app uses this)
            if (! Schema::hasColumn('notifications', 'read')) {
                $table->boolean('read')->default(false);
            }

            // Add sent_at timestamp
            if (! Schema::hasColumn('notifications', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
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
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('notifications', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('notifications', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('notifications', 'read')) {
                $table->dropColumn('read');
            }
            if (Schema::hasColumn('notifications', 'sent_at')) {
                $table->dropColumn('sent_at');
            }
        });
    }
};
