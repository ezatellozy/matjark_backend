<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_order_id')->nullable();
            $table->foreign('return_order_id')->references('id')->on('return_orders')->onDelete('cascade');
            $table->unsignedBigInteger('order_product_id')->nullable();
            $table->foreign('order_product_id')->references('id')->on('order_products')->onDelete('cascade');
            $table->unsignedBigInteger('product_detail_id')->nullable();
            $table->foreign('product_detail_id')->references('id')->on('product_details')->onDelete('set null');
            $table->double('quantity')->default(0);
            $table->double('price', 8, 2)->nullable(); // for one item
            $table->string('status')->default('waiting'); // accepted , rejected , received
            $table->string('reject_reason');
            $table->softDeletes();
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
        Schema::dropIfExists('return_order_products');
    }
}
