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
    // database/migrations/XXXX_create_requisitions_table.php
public function up()
{
    Schema::create('requisitions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('requisition_no')->nullable();
        $table->string('item_name');
        $table->text('description');
        $table->integer('quantity');
        $table->enum('urgency', ['low', 'medium', 'high'])->default('medium');
        $table->enum('status', ['pending', 'bought', 'done', 'paid'])->default('pending');
        $table->string('receipt_path')->nullable();
        $table->text('notes')->nullable();
        $table->boolean('received_confirmed')->default(false);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisitions');
    }
};
