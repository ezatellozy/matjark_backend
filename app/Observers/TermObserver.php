<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Term;

class TermObserver
{
    public function saved(Term $term)
    {
        foreach(config('translatable.locales') as $locale){
            if (request()->has($locale.'.image')) {
                if ($term->media()->exists()) {
                    $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Term', 'app_mediaable_id' => $term->id, 'media_type' => 'image', 'option' => $locale])->first();
                    if($image){
                        $image->delete();
                        if (file_exists(storage_path('app/public/images/terms/'.$image->media))){
                            \File::delete(storage_path('app/public/images/terms/'.$image->media));
                        }
                    }
                }

                $image = uploadImg(request()->$locale['image'], 'terms');

                $term->media()->create(['media' => $image, 'media_type' => 'image', 'option' => $locale, 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
            }
        }
    }

    public function deleted(Term $term)
    {
        if ($term->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Term', 'app_mediaable_id' => $term->id])->each(function($image, $key) {
                if (file_exists(storage_path('app/public/images/terms/'.$image->media))){
                    \File::delete(storage_path('app/public/images/terms/'.$image->media));
                }
                $image->delete();
            });
        }
    }
}
