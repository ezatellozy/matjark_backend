<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Category;

class CategoryObserver
{
    public function saved(Category $category)
    {
        if (request()->hasFile('image')) {
            if ($category->media()->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Category','app_mediaable_id' => $category->id ,'media_type' => 'image'])->first();
                $image->delete();
                if (file_exists(storage_path('app/public/images/categories/'.$image->media))){
                    \File::delete(storage_path('app/public/images/categories/'.$image->media));
                    $image->delete();
                }
            }

            $image = uploadImg(request()->image, 'categories');
            $category->media()->create(['media' => $image,'media_type' => 'image', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
        }
    }

    public function deleted(Category $category)
    {
        if ($category->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Category','app_mediaable_id' => $category->id ,'media_type' => 'image'])->first();
            if (file_exists(storage_path('app/public/images/categories/'.$image->media))){
                \File::delete(storage_path('app/public/images/categories/'.$image->media));
            }
            $image->delete();
        }
    }
}
