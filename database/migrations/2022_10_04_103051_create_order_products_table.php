<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->unsignedBigInteger('order_price_detail_id')->nullable();
            $table->foreign('order_price_detail_id')->references('id')->on('order_price_details')->onDelete('cascade');

            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('product_detail_id');
            $table->foreign('product_detail_id')->references('id')->on('product_details')->onDelete('cascade');


            $table->unsignedBigInteger('flash_sale_product_id')->nullable();
            $table->foreign('flash_sale_product_id')->references('id')->on('flash_sale_products')->onDelete('cascade');
            $table->text('flash_sale_data')->nullable();
            $table->double('flash_sale_percentage', 6, 2)->nullable();
            $table->double('flash_sale_price', 8, 2)->nullable();
            $table->double('quantity')->default(0);



            $table->unsignedBigInteger('offer_id')->nullable();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->text('offer_data')->nullable();
            $table->double('offer_percentage', 6, 2)->nullable();
            $table->double('offer_price', 8, 2)->nullable();
            

            $table->double('price', 8, 2)->nullable(); // for one item
            $table->double('total_product_price_before', 8, 2)->nullable();
            $table->double('total_price', 8, 2)->nullable();
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
        Schema::dropIfExists('order_products');
    }
}
