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
        Schema::table('requisitions', function (Blueprint $table) {
            // ✅ ONLY ADD received_confirmed (notes already exists)
            $table->boolean('received_confirmed')->default(false)->after('notes');
        });
    }

    public function down()
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn('received_confirmed');
        });
    }
};

