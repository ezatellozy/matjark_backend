<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAboutMetaDataTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('about_meta_data_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('about_meta_data_id');
            $table->string('meta_tag');
            $table->string('meta_title');
            $table->text('meta_description')->nullable();
            $table->string('locale')->index();
            $table->unique(['about_meta_data_id', 'locale']);
            $table->foreign('about_meta_data_id')->references('id')->on('about_meta_data')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('about_meta_translations');
    }
}
