<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToOrderRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_rates', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('comment'); // pending , accepted , rejected
            $table->string('reject_reason')->nullable()->after('status'); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_rates', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('reject_reason');

        });
    }
}
