<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Slider;

class SliderObserver
{
    public function saved(Slider $slider)
    {
        foreach(config('translatable.locales') as $locale){
            if (request()->has($locale.'.image')) {
                if ($slider->media()->exists()) {
                    $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Slider', 'app_mediaable_id' => $slider->id, 'media_type' => 'image', 'option' => $locale])->first();
                    if($image){
                        $image->delete();
                        if (file_exists(storage_path('app/public/images/sliders/'.$image->media))){
                            \File::delete(storage_path('app/public/images/sliders/'.$image->media));
                        }
                    }
                }

                $image = uploadImg(request()->$locale['image'], 'sliders');

                $slider->media()->create(['media' => $image, 'media_type' => 'image', 'option' => $locale, 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
            }
        }
    }

    public function deleted(Slider $slider)
    {
        if ($slider->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Slider', 'app_mediaable_id' => $slider->id])->each(function($image, $key) {
                if (file_exists(storage_path('app/public/images/sliders/'.$image->media))){
                    \File::delete(storage_path('app/public/images/sliders/'.$image->media));
                }
                $image->delete();
            });
        }
    }
}
