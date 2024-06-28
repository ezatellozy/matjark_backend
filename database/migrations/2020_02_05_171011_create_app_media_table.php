<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_media', function (Blueprint $table) {
            $table->id();
            $table->morphs('app_mediaable');
            $table->text('media')->nullable();
            $table->string('media_type')->nullable(); // image - video - audio - location .....
            $table->string('short_link')->nullable();
            $table->string('option')->nullable();
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
        Schema::dropIfExists('app_media');
    }
}
