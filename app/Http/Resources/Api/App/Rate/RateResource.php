<?php

namespace App\Http\Resources\Api\App\Rate;

use App\Http\Resources\Api\App\Product\SimpleProductDetailsResource;
use App\Http\Resources\Api\App\User\SenderResource;
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
            
        ];
    }
}
