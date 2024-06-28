<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Color;
use Illuminate\Support\Facades\File;

class ColorObserver
{
    public function saved(Color $color)
    {
        if (request()->hasFile('image')) {
            if ($color->media()->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Color','app_mediaable_id' => $color->id ,'media_type' => 'image'])->first();
                $image->delete();
                if (file_exists(storage_path('app/public/images/colors/'.$image->media))){
                    File::delete(storage_path('app/public/images/colors/'.$image->media));
                    $image->delete();
                }
            }

            $image = uploadImg(request()->image, 'colors');
            $color->media()->create(['media' => $image,'media_type' => 'image', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
        }
    }

    public function deleted(Color $color)
    {
        if ($color->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Color','app_mediaable_id' => $color->id ,'media_type' => 'image'])->first();
            if (file_exists(storage_path('app/public/images/colors/'.$image->media))){
                File::delete(storage_path('app/public/images/colors/'.$image->media));
            }
            $image->delete();
        }
    }
}
