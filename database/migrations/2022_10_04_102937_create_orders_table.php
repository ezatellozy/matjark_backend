<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('is_payment')->default('not_paid');   //  not_paid , paid
            $table->string('transactionId')->nullable();
            $table->string('pay_type')->nullable(); // wallet,card,cash
            $table->string('status')->default('pending'); // pending , admin_accept , admin_rejected, admin_cancel , client_cancel  , admin_shipping , admin_delivered , client_finished
            $table->unsignedBigInteger('address_id')->nullable();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->text('address_data')->nullable();
            $table->text('address_note')->nullable();
            $table->text('order_status_times')->nullable();
            $table->text('admin_reject_reason')->nullable();
            $table->text('user_cancel_reason')->nullable();
            $table->text('note')->nullable();
            $table->double('distance', 8, 2)->nullable();
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
        Schema::dropIfExists('orders');
    }
}
