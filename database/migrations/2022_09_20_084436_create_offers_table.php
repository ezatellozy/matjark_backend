<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('display_platform')->nullable(); // app  ,website , both
            $table->string('type')->nullable(); // buy_x_get_y ,fix_amount , percentage
            $table->integer('ordering')->nullable();
            $table->integer('num_of_use')->default(1);
            $table->integer('remain_use')->default(0)->nullable();
            $table->integer('max_use')->default(1)->nullable();  // for user
            $table->boolean('is_with_coupon')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('offer_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id');
            $table->string('name')->nullable();
            $table->string('desc')->nullable();
            $table->string('slug')->nullable();
            $table->string('locale')->index();
            $table->unique(['offer_id', 'locale']);
            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
        Schema::dropIfExists('offer_translations');
    }
}
