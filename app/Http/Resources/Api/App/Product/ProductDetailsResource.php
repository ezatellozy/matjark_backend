<?php

namespace App\Http\Resources\Api\App\Product;

use App\Http\Resources\Api\App\Help\{ColorResource, SizeResource};
use App\Models\FavouriteProduct;
use App\Models\OrderRate;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
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
            // 'color' => new ColorResource($this->color),
            'size' => new SizeResource($this->size),
            'price' => (float)$this->price,
            'currency' => 'SAR',
            'rate_avg' => (float)$this->rate_avg,
            'num_of_reviews' => (int)OrderRate::where('product_detail_id', $this->id)->count(),
            'have_sale' =>  $this->have_sale,
            'quantity' => (float)$this->quantity,
            'code' => (string)$this->code,
            'images' => $this->images,
            'is_fav' =>   $fav,
            'features' => $this->features ?  ProductFeaturesResource::collection($this->features) : null,
        ];
    }
}
