<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceWithVatToReturnOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_order_products', function (Blueprint $table) {
            $table->float('vat_percentage',8,2)->nullable();
            $table->float('vat_price',8,2)->nullable();
            $table->float('price_with_vat',8,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_order_products', function (Blueprint $table) {
            $table->dropColumn('vat_percentage');
            $table->dropColumn('vat_price');
            $table->dropColumn('price_with_vat');
        });
    }
}
