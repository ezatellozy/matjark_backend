<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_id')->constrained('order_rates')->cascadeOnDelete();
            $table->text('media')->nullable();
            $table->string('media_type')->nullable(); // image - video
            $table->string('short_link')->nullable();
            $table->string('option')->nullable();
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
        Schema::dropIfExists('rate_images');
    }
}
