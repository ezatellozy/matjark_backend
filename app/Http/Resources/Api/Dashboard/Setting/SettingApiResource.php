<?php

namespace App\Http\Resources\Api\Dashboard\Setting;

use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $type = $request->type;

        $images = ['website_logo','website_fav_icon','website_background_image','mobile_logo'];

        $settings = Setting::latest()->get();

        $data = [];

        foreach($settings as $k)
        {

            if (str_starts_with($k['key'], $type) || $type == 'all') {

                if(in_array($k['key'],$images)) {

                    $image = $k['value'] ? asset('storage/images/setting/'.$k['value']) : null;

                    $data[$k['key']] = $image;

                } else {

                    $data[$k['key']] = $k['value'];

                }

            } elseif ($k['key'] == 'tax_number') {

                $data[$k['key']] = $k['value'];
            }

        }

        return $data;
    }
}
