<?php

namespace App\Http\Controllers\Api\Dashboard\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Setting\SettingRequest;
use App\Http\Resources\Api\Dashboard\Setting\SettingResource;
use App\Models\Setting;
use Intervention\Image\Facades\Image as Image;

class SettingController extends Controller
{
    public function index()
    {
        return (new SettingResource(null))->additional(['status' => 'success', 'message' => '']);
    }

    public function store(SettingRequest $request)
    {
        $images = ['website_logo','website_fav_icon','website_background_image','mobile_logo'];
        
        foreach ($request->validated() as $key => $value) {
            
            if(in_array($key,$images)) {

                // $image = uploadImg($request->{$key},'setting');
                // $dist = storage_path('app/public/images/setting/');
                // $image = Image::make($request->{$key})->save($dist . ($request->{$key})->hashName());

                if($request->{$key} != null) {

                    $dist = storage_path('app/public/images/setting/');
                    $image = uniqid() . '.' . ($request->{$key})->extension();
                    // Image::make($request->{$key})->save($dist.'/'.$image);

                    request()->file($key)->move($dist, $image);

                    

                    Setting::updateOrCreate(
                        ['key' => trim($key)], 
                        ['value' => $image]
                    );
                }
                 
                

            } else {

                Setting::updateOrCreate(
                    ['key' => trim($key)], 
                    ['value' => $value]
                );
            }
        }

        return (new SettingResource(null))->additional(['status' => 'success', 'message' => trans('dashboard.update.success')]);
    }
}
