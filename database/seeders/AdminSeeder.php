<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'fullname' => "admin",
            'phone' => "1234567810",
            'email' => "admin1@info.com",
            'is_active' => 1,
            'is_ban' => 0,
            'email_verified_at' =>now()->addDay(rand(1,6)),
            'password' => '123456789', // secret
            'user_type' => 'superadmin', // secret
            'gender' => 'male', // secret
            'remember_token' => Str::random(10),
        ]);
    }
}
