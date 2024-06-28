<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('phone_code', 5)->nullable();
            $table->string('short_name')->nullable();
            $table->string('show_phonecode')->nullable();
            $table->string('flag')->nullable();
            $table->polygon('area')->nullable();
            $table->string('phone_number_limit')->nullable();
            $table->enum('continent', ['africa', 'europe', 'asia', 'south_america', 'north_america', 'australia'])->default('asia');
            $table->foreignId('added_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('country_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('currency')->nullable();
            $table->string('nationality')->nullable();
            $table->string('locale')->index();
            $table->unique(['country_id', 'locale']);
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('country_translations');
    }
}
