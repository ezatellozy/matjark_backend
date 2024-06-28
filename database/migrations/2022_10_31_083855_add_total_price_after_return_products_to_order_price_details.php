<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalPriceAfterReturnProductsToOrderPriceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_price_details', function (Blueprint $table) {
            $table->double('total_price_after_return_products', 8, 2)->nullable()->after('shipping_price'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_price_details', function (Blueprint $table) {
            $table->dropColumn('total_price_after_return_products');
        });
    }
}
