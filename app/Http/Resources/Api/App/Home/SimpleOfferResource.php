<?php

namespace App\Http\Resources\Api\App\Home;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this->buyToGetOffer);
        return [
            'id' => (int)$this->id,
            // 'image' => $this->image,
            'image' => app()->getLocale() == 'en'  ?  (string) $this->app_image_en : (string) $this->app_image_ar,
            'type' => (string)$this->type,
            'discount_type' => $this->type == 'buy_x_get_y'?  (string)@$this->buyToGetOffer->discount_type :  (string)@$this->discountOfOffer->discount_type,
            'discount_amount' => $this->type == 'buy_x_get_y'?  (float)@$this->buyToGetOffer->discount_amount :  (string)@$this->discountOfOffer->discount_amount,
        ];
    }
}
