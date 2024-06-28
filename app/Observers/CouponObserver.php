<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Coupon;
use Illuminate\Support\Facades\File;

class CouponObserver
{
    public function saved(Coupon $coupon)
    {
        if (request()->hasFile('image')) {
            if ($coupon->media()->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Coupon','app_mediaable_id' => $coupon->id ,'media_type' => 'image'])->first();
                $image->delete();
                if (file_exists(storage_path('app/public/images/coupons/'.$image->media))){
                    File::delete(storage_path('app/public/images/coupons/'.$image->media));
                    $image->delete();
                }
            }

            $image = uploadImg(request()->image, 'coupons');
            $coupon->media()->create(['media' => $image,'media_type' => 'image', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
        }
    }

    public function deleted(Coupon $coupon)
    {
        if ($coupon->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Coupon','app_mediaable_id' => $coupon->id ,'media_type' => 'image'])->first();
            if (file_exists(storage_path('app/public/images/coupons/'.$image->media))){
                File::delete(storage_path('app/public/images/coupons/'.$image->media));
            }
            $image->delete();
        }
    }
}
