<?php

namespace App\Http\Resources\Api\Dashboard\Offer_Old;

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
            'id'              => $this->id,
            'start_at'        => $this->start_at ? $this->start_at->format('Y-m-d') : null,
            'end_at'          => $this->end_at ? $this->end_at->format('Y-m-d') : null,
            'is_active'       => (bool) $this->is_active,
            'discount_type'   => (string) $this->discount_type,
            'discount_amount' => (double) $this->discount_amount,
            'max_use'         => (int) $this->max_use,
            'num_of_use'      => (int) $this->num_of_use,
            'remain_use'      => (int) $this->remain_use,
            'ordering'        => (int) $this->ordering,
            'image'           => (string) $this->image,
            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
