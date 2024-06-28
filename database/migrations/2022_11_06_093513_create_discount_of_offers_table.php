<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountOfOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_of_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
            $table->string('discount_type')->nullable(); // value ,percentage
            $table->double('discount_amount')->default(0)->nullable();
            $table->double('max_discount')->default(0)->nullable();
            $table->string('apply_on'); // all , special_products, special_categories  , special_payment
            $table->json('apply_ids')->nullable();
            $table->string('payment_type')->nullable(); // wallet,card,cash
            $table->string('min_type')->nullable();   //quantity_of_products , amount_of_total_price
            $table->string('min_value')->nullable();
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
        Schema::dropIfExists('discount_of_offers');
    }
}
