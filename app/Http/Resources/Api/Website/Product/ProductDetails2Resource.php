<?php

namespace App\Http\Resources\Api\Website\Product;

use App\Http\Resources\Api\App\Help\{ColorResource, Size2Resource, SizeResource};
use App\Http\Resources\Api\Website\Rate\RateResource;
use App\Models\{FavouriteProduct, OrderRate};
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\CommonQuestion\CommonQuestionResource;

class ProductDetails2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $sizes = $this->where('product_id',$this->product_id)->where('color_id',$this->color_id)->get();
      
        return [
            'id' => (int)$this->id,
            'sizes' => Size2Resource::collection($sizes),
            'images' => $this->images,                  
        ];
    }
}
