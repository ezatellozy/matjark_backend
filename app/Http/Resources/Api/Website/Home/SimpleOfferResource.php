<?php

namespace App\Http\Resources\Api\Website\Home;

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

        return [
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'desc' => (string)$this->desc,
            // 'image' => $this->image,
            'image' => app()->getLocale() == 'en'  ?  (string) $this->web_image_en : (string) $this->web_image_ar,
             'discount_type' =>  (string)$this->discount_type,
            'discount_amount' => (float)$this->discount_amount,
        ];
    }
}
