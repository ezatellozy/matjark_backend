<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Country, City, CityTranslation};

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::create([
            'ar' => [
                "name" => "أبو ظبي",
            ],
            'en' => [
                "name" => "Abu Dhabi",
            ],
            'country_id' => 1
        ]);

        City::create([
            'ar' => [
                "name" => "دبي",
            ],
            'en' => [
                "name" => "Dubai",
            ],
            'country_id' => 1
        ]);

    }
}
