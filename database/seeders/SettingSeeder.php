<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\Setting::create([
        //     'key' => 'about_ar',
        //     'value' => 'about_ar'
        // ]);
        // \App\Models\Setting::create([
        //     'key' => 'about_en',
        //     'value' => 'about_en'
        // ]);
        // \App\Models\Setting::create([
        //     'key' => 'terms_ar',
        //     'value' => 'terms_ar'
        // ]);
        // \App\Models\Setting::create([
        //     'key' => 'terms_en',
        //     'value' => 'terms_en'
        // ]);
        // \App\Models\Setting::create([
        //     'key' => 'privacy_ar',
        //     'value' => 'privacy_ar'
        // ]);
        // \App\Models\Setting::create([
        //     'key' => 'privacy_en',
        //     'value' => 'privacy_en'
        // ]);

        /////////////////////////////////////////////////////////////////

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_primary_color',
        // ], [
        //     'key' => 'website_primary_color',
        //     'value' => '#339989'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_secondary_color',
        // ], [
        //     'key' => 'website_secondary_color',
        //     'value' => '#339989'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_bg_light_color',
        // ], [
        //     'key' => 'website_bg_light_color',
        //     'value' => '#339989'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_bg_gray_color',
        // ], [
        //     'key' => 'website_bg_gray_color',
        //     'value' => '#333'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_title_color',
        // ], [
        //     'key' => 'website_title_color',
        //     'value' => '#000'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_text_color',
        // ], [
        //     'key' => 'website_text_color',
        //     'value' => '#000'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_sub_text_color',
        // ], [
        //     'key' => 'website_sub_text_color',
        //     'value' => '#28a745'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_border_color',
        // ], [
        //     'key' => 'website_border_color',
        //     'value' => '#ffc107'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_golden_color',
        // ], [
        //     'key' => 'website_golden_color',
        //     'value' => '#fff'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'website_theme_color',
        // ], [
        //     'key' => 'website_theme_color',
        //     'value' => '#fff'
        // ]);


        // /////////////////////////////////////////////////////////////////
        // /////////////////////////////////////////////////////////////////

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_primary_color',
        // ], [
        //     'key' => 'mobile_primary_color',
        //     'value' => '#339989'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_secondary_color',
        // ], [
        //     'key' => 'mobile_secondary_color',
        //     'value' => '#339989'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_tertiary_color',
        // ], [
        //     'key' => 'mobile_tertiary_color',
        //     'value' => '#339989'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_light_border_color',
        // ], [
        //     'key' => 'mobile_light_border_color',
        //     'value' => '#333'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_font_color',
        // ], [
        //     'key' => 'mobile_font_color',
        //     'value' => '#000'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_light_font_color',
        // ], [
        //     'key' => 'mobile_light_font_color',
        //     'value' => '#333'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_success_color',
        // ], [
        //     'key' => 'mobile_success_color',
        //     'value' => '#28a745'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_warning_color',
        // ], [
        //     'key' => 'mobile_warning_color',
        //     'value' => '#ffc107'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_dark_border_color',
        // ], [
        //     'key' => 'mobile_dark_border_color',
        //     'value' => '#000'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_white_color',
        // ], [
        //     'key' => 'mobile_white_color',
        //     'value' => '#333'
        // ]);

        // \App\Models\Setting::updateOrCreate([
        //     'key' => 'mobile_footer_color',
        // ], [
        //     'key' => 'mobile_footer_color',
        //     'value' => '#777'
        // ]);

        \App\Models\Setting::updateOrCreate([
            'key' => 'tax_number',
        ], [
            'key' => 'tax_number',
            'value' => '112233445566789'
        ]);

    }
}
