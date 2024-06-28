<?php

namespace App\Http\Resources\Api\Dashboard\Offer;

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
            'id'               => $this->id,
            'name'             => (string) $this->name,
            'desc'             => (string) $this->desc,
            'start_at'         => $this->start_at ? $this->start_at->format('Y-m-d') : null,
            'end_at'           => $this->end_at ? $this->end_at->format('Y-m-d') : null,
            'start_time'           => $this->start_at ? $this->start_at->format('H:i') : null,
            'end_time'             => $this->end_at ? $this->end_at->format('H:i') : null,

            'is_active'        => (bool) $this->is_active,
            'max_use'          => (int) $this->max_use,
            'num_of_use'       => (int) $this->num_of_use,
            'remain_use'       => (int) $this->remain_use,
            'ordering'         => (int) $this->ordering,
            // 'image'            => (string) $this->image,
            'app_image'             =>  app()->getLocale() == 'en'  ?  (string) $this->app_image_en : (string) $this->app_image_ar,
            'web_image'             => app()->getLocale() == 'en'  ?  (string) $this->web_image_en : (string) $this->web_image_ar,
            'type'             => (string) $this->type,
            'is_with_coupon'   => (bool) $this->is_with_coupon,
            'display_platform' => (string) $this->display_platform,
            'created_at'       => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
