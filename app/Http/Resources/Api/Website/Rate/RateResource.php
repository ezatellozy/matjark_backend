<?php

namespace App\Http\Resources\Api\Website\Rate;

use App\Http\Resources\Api\Website\Product\SimpleProductDetailsResource;
use App\Http\Resources\Api\App\User\SenderResource;
use Carbon\Carbon;
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
            'id' => (int)$this->id,
            'rate' => (float)$this->rate,
            'comment' => (string)$this->comment,
            'rate_images' => $this->images,
            'user' => new SenderResource($this->user),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'color' => $this->productDetail != null && $this->productDetail->color != null ? $this->productDetail->color->name : '',
            'size' => $this->productDetail != null && $this->productDetail->size != null ? $this->productDetail->size->name : '',
        ];
    }
}
