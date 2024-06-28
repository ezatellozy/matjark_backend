<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meta_id');
            $table->string('meta_tag');
            $table->string('meta_title');
            $table->text('meta_description')->nullable();
            $table->string('locale')->index();
            $table->unique(['meta_id', 'locale']);
            $table->foreign('meta_id')->references('id')->on('metas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_translations');
    }
}
