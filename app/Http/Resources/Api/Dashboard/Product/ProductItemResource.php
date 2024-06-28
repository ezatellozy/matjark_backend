<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Models\ProductMedia;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\CommonQuestion\SimpleCommonQuestionResource;

class ProductItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = ProductMedia::where(['product_id' => $this->id, 'option' => null])->first();

        return [
            'id'         => (int) $this->id,
            'name'       => (string) $this->name,
            'image'      => asset('storage/images/products/'.$this->id.'/'.$image->product_details_id.'/'.$image->media),
        ];
    }
}
