<?php

namespace App\Http\Resources\Api\Provider\Offer;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $locales = [];

        foreach (config('translatable.locales') as $locale) {
            $locales[$locale]['name'] = $this->translate($locale)->name;
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
            
        }

        $data = [
            'id'                => $this->id,
            'name'              => (string) $this->name,
            'desc'              => (string) $this->desc,
            'start_at'          => $this->start_at ? $this->start_at->format('Y-m-d') : null,
            'end_at'            => $this->end_at ? $this->end_at->format('Y-m-d') : null,
            'start_time'           => $this->start_at ? $this->start_at->format('H:i') : null,
            'end_time'             => $this->end_at ? $this->end_at->format('H:i') : null,
            
            'is_active'         => (bool) $this->is_active,
            'max_use'           => (int) $this->max_use,
            'num_of_use'        => (int) $this->num_of_use,
            'remain_use'        => (int) $this->remain_use,
            'ordering'          => (int) $this->ordering,
            // 'image'             => (string) $this->image,
            'app_image_ar'             => (string) $this->app_image_ar,
            'app_image_en'             => (string) $this->app_image_en,
            'web_image_en'             => (string) $this->web_image_en,
            'web_image_ar'             => (string) $this->web_image_ar,
            'type'              => (string) $this->type,
            'is_with_coupon'    => (bool) $this->is_with_coupon,
            'display_platform'  => (string) $this->display_platform,
            'by_to_get'         => $this->buyToGetOffer ? new BuyToGetOfferResource($this->buyToGetOffer) : null,
            'discount_of_offer' => $this->discountOfOffer ? new DiscountOfOfferResource($this->discountOfOffer) : null,
            'created_at'        => $this->created_at ? $this->created_at->format('Y-m-d') : null,

        ];

        return $data + $locales;
    }
}
