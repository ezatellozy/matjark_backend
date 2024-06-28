<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone_code')->nullable();
            $table->string('phone')->nullable();
            // $table->string('phone_code')->nullable();

            $table->string('whatsapp')->nullable();
            $table->string('image')->nullable();
            $table->string('cover')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_admin_active_user')->default(true);
            $table->boolean('is_ban')->default(false)->nullable();
            $table->text('ban_reason')->nullable();

            $table->string('reset_code')->nullable();
            $table->string('verified_code')->nullable();

            $table->string('user_type')->nullable(); //admin - superadmin - client - driver - provider...
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->float('rate_avg',5,2)->default(0);

            $table->string('referral_code')->nullable();

            $table->integer('register_complete_step')->default(1);
            $table->string('locale')->default('en')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
