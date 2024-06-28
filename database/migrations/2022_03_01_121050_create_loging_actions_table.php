<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogingActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loging_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable() ;  
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
         
            $table->longText('link')->nullable() ; 
             $table->timestamps();
        });
        Schema::create('loging_action_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loging_action_id');
            $table->longText('title');
            $table->string('locale')->index();
            $table->unique(['loging_action_id', 'locale']);
            $table->foreign('loging_action_id')->references('id')->on('loging_actions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loging_action_translations');
        Schema::dropIfExists('loging_actions');
    }
}