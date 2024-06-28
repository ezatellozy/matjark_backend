<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Company::updateOrCreate([
            'tax_number' => '123456789'
        ],[
            'name' => 'Al-Almiya-Alhura',
            'tax_number' => '123456789'
        ]);
        
    }
}
