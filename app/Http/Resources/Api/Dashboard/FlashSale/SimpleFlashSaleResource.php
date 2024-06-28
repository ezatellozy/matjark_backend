<?php

namespace App\Http\Resources\Api\Dashboard\FlashSale;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleFlashSaleResource extends JsonResource
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
            'id'         => (int) $this->id,
            'start_at'   => $this->start_at->format('Y-m-d'),
            'end_at'     => $this->end_at->format('Y-m-d'),
            'start_time'           => $this->start_at ? $this->start_at->format('H:i') : null,
            'end_time'             => $this->end_at ? $this->end_at->format('H:i') : null,
            'is_active'  => (bool) $this->is_active,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
