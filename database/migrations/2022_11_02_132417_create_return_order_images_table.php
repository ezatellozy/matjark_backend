<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnOrderImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_order_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_order_id')->nullable();
            $table->foreign('return_order_id')->references('id')->on('return_orders')->onDelete('cascade');
            $table->unsignedBigInteger('order_product_id')->nullable();
            $table->foreign('order_product_id')->references('id')->on('order_products')->onDelete('cascade');
            $table->text('media')->nullable();
            $table->string('media_type')->nullable(); // image - video
            $table->string('short_link')->nullable();
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
        Schema::dropIfExists('return_order_images');
    }
}
