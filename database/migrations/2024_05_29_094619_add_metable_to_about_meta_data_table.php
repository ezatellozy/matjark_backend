<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetableToAboutMetaDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('about_meta_data', function (Blueprint $table) {
            $table->morphs('metable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('about_meta_data', function (Blueprint $table) {
            $table->dropColumn('metable_type');
            $table->dropColumn('metable_id');
        });
    }
}
