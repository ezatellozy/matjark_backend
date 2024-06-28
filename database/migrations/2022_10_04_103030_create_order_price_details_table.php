<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPriceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_price_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // $table->unsignedBigInteger('coupon_id')->nullable();
            // $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            // $table->text('coupon_data')->nullable();
            // $table->double('coupon_percentage', 6, 2)->nullable();
            // $table->double('coupon_price', 8, 2)->nullable();

            $table->double('discount_value', 8, 2)->default(0);   // for falsh sale total  discount or final discount value for offer or coupon 
            $table->double('vat_percentage', 6, 2)->nullable();
            $table->double('vat_price', 8, 2)->nullable();
            $table->double('total_product_price_before', 8, 2)->nullable();
            $table->double('total_price', 8, 2)->nullable();
            $table->double('shipping_price', 8, 2)->nullable();
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
        Schema::dropIfExists('order_price_details');
    }
}
