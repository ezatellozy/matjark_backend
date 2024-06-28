<?php

namespace App\Observers;

use App\Models\OrderRate;
use Illuminate\Support\Facades\File;

class OrderRateObserver
{
    /**
     * Handle the OrderRate "created" event.
     *
     * @param  \App\Models\OrderRate  $orderRate
     * @return void
     */

    public function saved(OrderRate $rateImages)
    {
        if (request()->hasFile('rate_images')) {
            $images = request()->rate_images;
            foreach ($images as $image) {
                $uploadImage = upload_single_file($image, 'app/public/images/rateImages');
                $rateImages->rateImages()->create(['media' => $uploadImage, 'media_type' => 'image', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
            }
        }
    }

    /**
     * Handle the OrderRate "deleted" event.
     *
     * @param  \App\Models\OrderRate  $orderRate
     * @return void
     */
    public function deleted(OrderRate $rate)
    {
        if ($rate->rateImages()->exists()) {
            $image = $rate->rateImages()->where(['rate_id' => $rate->id])->each(function($image, $key) {
                if (file_exists(storage_path('app/public/images/rateImages/'.$image->media))){
                    File::delete(storage_path('app/public/images/rateImages/'.$image->media));
                }
                $image->delete();
            });
        }
    }
}
