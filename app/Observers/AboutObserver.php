<?php

namespace App\Observers;

use App\Models\About;
use App\Models\AppMedia;

class AboutObserver
{
    public function saved(About $about)
    {
        foreach(config('translatable.locales') as $locale){
            if (request()->has($locale.'.image')) {
                if ($about->media()->exists()) {
                    $image = AppMedia::where(['app_mediaable_type' => 'App\Models\About', 'app_mediaable_id' => $about->id, 'media_type' => 'image', 'option' => $locale])->first();
                    if($image){
                        $image->delete();
                        if (file_exists(storage_path('app/public/images/abouts/'.$image->media))){
                            \File::delete(storage_path('app/public/images/abouts/'.$image->media));
                        }
                    }
                }

                $image = uploadImg(request()->$locale['image'], 'abouts');

                $about->media()->create(['media' => $image, 'media_type' => 'image', 'option' => $locale, 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
            }
        }
    }

    public function deleted(About $about)
    {
        if ($about->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\About', 'app_mediaable_id' => $about->id])->each(function($image, $key) {
                if (file_exists(storage_path('app/public/images/abouts/'.$image->media))){
                    \File::delete(storage_path('app/public/images/abouts/'.$image->media));
                }
                $image->delete();
            });
        }
    }
}
