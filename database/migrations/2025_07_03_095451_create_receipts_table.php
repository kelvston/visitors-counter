<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            // Define the foreign key columns FIRST
            $table->unsignedBigInteger('sale_item_id')->unique()->comment('Reference to the sale item');
            $table->unsignedBigInteger('user_id')->comment('User who generated the receipt');

            // Receipt details
            $table->string('receipt_number')->unique();
            $table->text('receipt_data')->nullable()->comment('JSON or HTML data of receipt content');
            $table->decimal('total_amount', 15, 2);
            $table->timestamp('printed_at')->nullable()->comment('When receipt was printed');

            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('sale_item_id')->references('id')->on('sale_items')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('receipts');
    }
};
