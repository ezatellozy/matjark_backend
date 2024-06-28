<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Offer;
use Illuminate\Support\Facades\File;

class OfferObserver
{
    public function saved(Offer $offer)
    {
        // if (request()->has('image')) {
        //     if ($offer->media()->exists()) {
        //         $image = AppMedia::where(['app_mediaable_type' => 'App\Models\offer', 'app_mediaable_id' => $offer->id, 'media_type' => 'image'])->first();
        //         if($image){
        //             $image->delete();
        //             if (file_exists(storage_path('app/public/images/offers/'.$image->media))){
        //                 File::delete(storage_path('app/public/images/offers/'.$image->media));
        //             }
        //         }
        //     }

        //     $image = uploadImg(request()->image, 'offers');

        //     $offer->media()->create(['media' => $image, 'media_type' => 'image']);
        // }


        if (request()->has('app_image_ar')) {
            if ($offer->media()->where('option','app_image_ar')->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\offer', 'option'=>'app_image_ar','app_mediaable_id' => $offer->id, 'media_type' => 'image'])->first();
                if($image){
                    $image->delete();
                    if (file_exists(storage_path('app/public/images/offers/'.$image->media))){
                        File::delete(storage_path('app/public/images/offers/'.$image->media));
                    }
                }
            }

            $image = uploadImg(request()->app_image_ar, 'offers');

            $offer->media()->create(['media' => $image, 'media_type' => 'image' , 'option'=>'app_image_ar', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
        }

        if (request()->has('app_image_en')) {
            if ($offer->media()->where('option','app_image_en')->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\offer', 'option'=>'app_image_en' ,'app_mediaable_id' => $offer->id, 'media_type' => 'image'])->first();
                if($image){
                    $image->delete();
                    if (file_exists(storage_path('app/public/images/offers/'.$image->media))){
                        File::delete(storage_path('app/public/images/offers/'.$image->media));
                    }
                }
            }

            $image = uploadImg(request()->app_image_en, 'offers');

            $offer->media()->create(['media' => $image, 'media_type' => 'image','option'=>'app_image_en']);
        }


        if (request()->has('web_image_ar')) {
            if ($offer->media()->where('option','web_image_ar')->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\offer',  'option'=>'web_image_ar' ,'app_mediaable_id' => $offer->id, 'media_type' => 'image'])->first();
                if($image){
                    $image->delete();
                    if (file_exists(storage_path('app/public/images/offers/'.$image->media))){
                        File::delete(storage_path('app/public/images/offers/'.$image->media));
                    }
                }
            }

            $image = uploadImg(request()->web_image_ar, 'offers');

            $offer->media()->create(['media' => $image, 'media_type' => 'image' ,'option'=>'web_image_ar']);
        }


        if (request()->has('web_image_en')) {
            if ($offer->media()->where('option','web_image_en')->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\offer','option'=>'web_image_en' , 'app_mediaable_id' => $offer->id, 'media_type' => 'image'])->first();
                if($image){
                    $image->delete();
                    if (file_exists(storage_path('app/public/images/offers/'.$image->media))){
                        File::delete(storage_path('app/public/images/offers/'.$image->media));
                    }
                }
            }

            $image = uploadImg(request()->web_image_en, 'offers');

            $offer->media()->create(['media' => $image, 'media_type' => 'image','option'=>'web_image_en']);
        }
    }

    public function deleted(Offer $offer)
    {
        if ($offer->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\offer', 'app_mediaable_id' => $offer->id])->each(function($image, $key) {
                if (file_exists(storage_path('app/public/images/offers/'.$image->media))){
                    File::delete(storage_path('app/public/images/offers/'.$image->media));
                }
                $image->delete();
            });
        }
    }
}
