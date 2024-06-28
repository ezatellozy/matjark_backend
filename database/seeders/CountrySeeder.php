<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Country, CountryTranslation};
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country = Country::create([
            'show_phonecode' => '971',
            'phone_code' => '971',
            'continent' => 'asia',
        ]);

        CountryTranslation::create(['name' => 'الامارات العربيه المتحدة', 'nationality' => 'اماراتى', 'currency' => 'درهم اماراتى', 'country_id' => $country->id, 'locale' => 'ar']);
        CountryTranslation::create(['name' => 'The United Arab Emirates', 'nationality' => 'Emirati', 'currency' => 'AED', 'country_id' => $country->id, 'locale' => 'en']);
    }
}
