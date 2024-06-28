<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Country;

class CountryObserver
{
    public function saved(Country $country)
    {
        if (request()->hasFile('image')) {
            if ($country->media()->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Country','app_mediaable_id' => $country->id ,'media_type' => 'image'])->first();
                $image->delete();
                if (file_exists(storage_path('app/public/images/countries/'.$image->media))){
                    \File::delete(storage_path('app/public/images/countries/'.$image->media));
                    $image->delete();
                }
            }
            $image = uploadImg(request()->image, 'countries');
            $country->media()->create(['media' => $image,'media_type' => 'image', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
        }
    }

    public function deleted(Country $country)
    {
        if ($country->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Country','app_mediaable_id' => $country->id ,'media_type' => 'image'])->first();
            if (file_exists(storage_path('app/public/images/countries/'.$image->media))){
                \File::delete(storage_path('app/public/images/countries/'.$image->media));
            }
            $image->delete();
        }
    }

    /**
     * Handle the Country "restored" event.
     *
     * @param  \App\Models\Country  $country
     * @return void
     */
    public function restored(Country $country)
    {
        //
    }

    /**
     * Handle the Country "force deleted" event.
     *
     * @param  \App\Models\Country  $country
     * @return void
     */
    public function forceDeleted(Country $country)
    {
        //
    }
}
