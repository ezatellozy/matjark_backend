<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('feature_value_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_value_id')->constrained('feature_values')->cascadeOnDelete();
            $table->string('value');
            $table->string('locale')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feature_values');
        Schema::dropIfExists('feature_value_translations');
    }
}
