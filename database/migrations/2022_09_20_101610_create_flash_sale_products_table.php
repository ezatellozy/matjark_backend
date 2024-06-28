<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlashSaleProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flash_sale_id');
            $table->foreign('flash_sale_id')->references('id')->on('flash_sales')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->unsignedBigInteger('product_detail_id')->nullable();
            $table->foreign('product_detail_id')->references('id')->on('product_details')->onDelete('set null');
            $table->integer('quantity')->default(0);
            $table->integer('quantity_for_user')->default(0);
            $table->integer('ordering')->default(0);
            $table->string('discount_type'); // value, percentage
            $table->double('discount_amount');
            $table->double('price_before')->default(0)->nullable();
            $table->double('price_after')->default(0)->nullable();
            $table->integer('sold')->default(0);
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
        Schema::dropIfExists('flash_sale_products');
    }
}
