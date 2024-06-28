<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyToGetOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buy_to_get_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->double('buy_quantity')->default(0);
            $table->string('buy_apply_on'); // all , special_products, special_categories  
            $table->json('buy_apply_ids')->nullable();
            $table->double('get_quantity')->default(0);
            $table->string('get_apply_on'); // all , special_products, special_categories  
            $table->json('get_apply_ids')->nullable();
            $table->string('discount_type')->nullable(); // free_product ,percentage
            $table->double('discount_amount')->default(0)->nullable();
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
        Schema::dropIfExists('buy_to_get_offers');
    }
}
