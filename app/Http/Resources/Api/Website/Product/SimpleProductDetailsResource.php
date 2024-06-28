<?php

namespace App\Http\Resources\Api\Website\Product;

use App\Http\Resources\Api\App\Help\{ColorResource, SizeResource};
use App\Models\FavouriteProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductDetailsResource extends JsonResource
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

        $product = $this->product()->withTrashed()->first();

        return [
            'id' => (int)$this->id,
            //'name' => $this->product->withTrashed() != null ? (string)$this->product->withTrashed()->name : '',
            'name' => $product != null ? $product->name : '',
            'desc' => (string)@$this->product->desc,
            'slug' => $product->slug,
            'price' => (float)$this->price,
            'currency' => 'SAR',
            'color' =>  new ColorResource($this->color),
            'size' => new SizeResource($this->size),
            // 'image'  => $this->image,
            'rate_avg' => (float)$this->rate_avg,

            'images' => $this->two_rand_images,
            'code' => (string)$this->code,
            'is_fav' =>   $fav ,
            'have_sale' =>  $this->have_sale,
            'quantity' => (float)$this->quantity,
            // 'product_id' => $this->product->id,
            'product_id' => $this->product_id,
        ];
    }
}
