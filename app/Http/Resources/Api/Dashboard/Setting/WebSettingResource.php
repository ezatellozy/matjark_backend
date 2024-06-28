<?php

namespace App\Http\Resources\Api\Dashboard\Setting;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class WebSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'key' => $this->key,
            'value' => $this->shouldDecode($this->key) ? json_decode($this->value)
                : ($this->shouldCast($this->key) ? (bool)$this->value : $this->value),
        ];
    }

    /**
     * check if the value should be decoded
     * @param $key
     * @return boolean
     */
    public function shouldDecode($key)
    {
        $keysToDecode = ['email', 'phone_number', 'store_address'];
        return in_array($key, $keysToDecode);
    }

    public function shouldCast($key)
    {
        $keysToCast = ['card', 'cash_on_delivery', 'top_bar_availability', 'slider_availability'
            , 'slider_multi_image', 'title_availability', 'footer_main_part_availability'];
        return in_array($key, $keysToCast);
    }
}
