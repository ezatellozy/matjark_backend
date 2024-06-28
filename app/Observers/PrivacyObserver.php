<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Privacy;

class PrivacyObserver
{
    public function saved(Privacy $privacy)
    {
        foreach(config('translatable.locales') as $locale){
            if (request()->has($locale.'.image')) {
                if ($privacy->media()->exists()) {
                    $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Privacy', 'app_mediaable_id' => $privacy->id, 'media_type' => 'image', 'option' => $locale])->first();
                    if($image){
                        $image->delete();
                        if (file_exists(storage_path('app/public/images/privacies/'.$image->media))){
                            \File::delete(storage_path('app/public/images/privacies/'.$image->media));
                        }
                    }
                }

                $image = uploadImg(request()->$locale['image'], 'privacies');

                $privacy->media()->create(['media' => $image, 'media_type' => 'image', 'option' => $locale, 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
            }
        }
    }

    public function deleted(Privacy $privacy)
    {
        if ($privacy->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Privacy', 'app_mediaable_id' => $privacy->id])->each(function($image, $key) {
                if (file_exists(storage_path('app/public/images/privacies/'.$image->media))){
                    \File::delete(storage_path('app/public/images/privacies/'.$image->media));
                }
                $image->delete();
            });
        }
    }
}
