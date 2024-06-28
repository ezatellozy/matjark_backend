<?php

namespace App\Http\Resources\Api\Dashboard\Rate;

use App\Http\Resources\Api\Dashboard\Order\SimpleOrderResource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductDetailResource;
use App\Http\Resources\Api\Help\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
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
            'id'          => (int)$this->id,
            'rate'        => (float)$this->rate,
            'status'      => (string) $this->status,
            'status_trans'      => trans('dashboard.rate_status.'.$this->status),
            'comment'     => (string)$this->comment,
            'rate_images' => $this->images,
            'user'        => $this->user ? new SimpleUserResource($this->user) : null,
            'product_details' => new SimpleProductDetailResource($this->productDetail),
        ];
    }
}
