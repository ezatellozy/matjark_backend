<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivaciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privacies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('ordering');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('privacy_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('privacy_id');
            $table->string('title');
            $table->text('desc');
            $table->string('slug')->nullable();
            $table->string('locale')->index();
            $table->unique(['privacy_id', 'locale']);
            $table->foreign('privacy_id')->references('id')->on('privacies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('privacies');
        Schema::dropIfExists('privacy_translations');
    }
}
