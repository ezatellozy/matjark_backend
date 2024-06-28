<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\ReturnOrder;

class ReturnOrderObserver
{
    /**
     * Handle the ReturnOrder "created" event.
     *
     * @param  \App\Models\ReturnOrder  $returnOrder
     * @return void
     */
    public function saved(ReturnOrder $returnOrder)
    {
        if (request()->hasFile('images')) {
            $images = request()->images;
            foreach ($images as $image) {
                $uploadImage = upload_single_file($image, 'app/public/images/return_orders');
                $returnOrder->returnOrderImages()->create(['media' => $uploadImage, 'media_type' => 'image', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
            }
        }

        // if (request()->hasFile('image')) {
        //     if ($returnOrder->media()->exists()) {
        //         $image = AppMedia::where(['app_mediaable_type' => 'App\Models\ReturnOrder','app_mediaable_id' => $returnOrder->id ,'media_type' => 'image'])->first();
        //         $image->delete();
        //         if (file_exists(storage_path('app/public/images/return_orders/'.$image->media))){
        //             \File::delete(storage_path('app/public/images/return_orders/'.$image->media));
        //             $image->delete();
        //         }
        //     }
        //     $image = uploadImg(request()->image, 'return_orders');
        //     $returnOrder->media()->create(['media' => $image,'media_type' => 'image']);
        // }
    }


    /**
     * Handle the ReturnOrder "updated" event.
     *
     * @param  \App\Models\ReturnOrder  $returnOrder
     * @return void
     */
    public function updated(ReturnOrder $returnOrder)
    {
        //
    }

    /**
     * Handle the ReturnOrder "deleted" event.
     *
     * @param  \App\Models\ReturnOrder  $returnOrder
     * @return void
     */
    public function deleted(ReturnOrder $returnOrder)
    {
        //
    }

    /**
     * Handle the ReturnOrder "restored" event.
     *
     * @param  \App\Models\ReturnOrder  $returnOrder
     * @return void
     */
    public function restored(ReturnOrder $returnOrder)
    {
        //
    }

    /**
     * Handle the ReturnOrder "force deleted" event.
     *
     * @param  \App\Models\ReturnOrder  $returnOrder
     * @return void
     */
    public function forceDeleted(ReturnOrder $returnOrder)
    {
        //
    }
}
