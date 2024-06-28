<?php

namespace App\Http\Resources\Api\App\Home;

use App\Http\Resources\Api\App\Help\ColorResource;
use App\Models\FavouriteProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $fav = auth()->guard('api')->user() != null ? (FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null, 'product_detail_id' => $this->id])->first()  != null ? true : false) : ($request->guest_token != null ? (FavouriteProduct::where(['guest_token' => $request->guest_token, 'product_detail_id' => $this->id, 'user_id' => null])->first()  != null ? true : false) : false);
        return [
            'id' => (int)$this->id,
            'price' => (float)$this->price,
            'currency' => 'SAR',
            'color' =>  new ColorResource($this->color),
            'images'  => $this->two_rand_images,
            'is_fav' => $fav,
            'quantity' => (float)$this->quantity,
            'have_sale' =>  $this->have_sale,
        ];
    }
}
