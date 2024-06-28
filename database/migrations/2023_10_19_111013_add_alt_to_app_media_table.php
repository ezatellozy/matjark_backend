<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAltToAppMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_media', function (Blueprint $table) {
            $table->string('alt_ar')->nullable()->after('option');
            $table->string('alt_en')->nullable()->after('option');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_media', function (Blueprint $table) {
            $table->dropColumn('alt_ar');
            $table->dropColumn('alt_en');
        });
    }
}
