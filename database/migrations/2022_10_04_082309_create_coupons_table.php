<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('discount_type'); // 'value', 'percentage'
            $table->double('discount_amount');
            $table->double('max_discount')->nullable();
            $table->integer('max_used_num')->nullable();
            $table->integer('num_of_used')->default(0);
            $table->integer('max_used_for_user')->nullable();
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->boolean('is_active')->default(true);
            $table->string('applly_coupon_on'); // all , special_products, except_products, special_categories, except_categories
            $table->json('apply_ids')->nullable();
            $table->string('addtion_options')->nullable(); // free_shipping
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('coupons');
    }
}
