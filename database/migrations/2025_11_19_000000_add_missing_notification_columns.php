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
        if (! Schema::hasTable('notifications')) {
            // If the table doesn't exist at all, create a minimal structure used by the app
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('type')->nullable();
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->text('data')->nullable();
                $table->boolean('read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
            return;
        }

        // Table exists — add any missing columns without touching existing ones
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->index();
            }

            if (! Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable();
            }

            if (! Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable();
            }

            if (! Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable();
            }

            if (! Schema::hasColumn('notifications', 'data')) {
                $table->text('data')->nullable();
            }

            if (! Schema::hasColumn('notifications', 'read')) {
                $table->boolean('read')->default(false);
            }

            if (! Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable();
            }

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
            if (Schema::hasColumn('notifications', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('notifications', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('notifications', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('notifications', 'data')) {
                $table->dropColumn('data');
            }
            if (Schema::hasColumn('notifications', 'read')) {
                $table->dropColumn('read');
            }
            if (Schema::hasColumn('notifications', 'read_at')) {
                $table->dropColumn('read_at');
            }
            if (Schema::hasColumn('notifications', 'sent_at')) {
                $table->dropColumn('sent_at');
            }
        });
    }
};
