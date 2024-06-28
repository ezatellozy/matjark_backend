<?php

namespace App\Http\Resources\Api\App\Home;

use App\Models\Category;
use App\Models\Offer;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use App\Http\Resources\Api\Dashboard\Offer\SimpleOfferResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if($this->item_type != 'products') {

            if($this->item_type == 'category') {
                $item = Category::find($this->item_id);
                $category_name = @$item->name;
                $item = $item ? new CategorySimpleResource($item) : null;
            } else {
                $item = Offer::find($this->item_id);
                $category_name = null;
                $item = $item ? new SimpleOfferResource($item) : null;
            }

        } else {
            $item = null;
            $category_name = null;
        }


        return [
            'id' => (int)$this->id,
            'type' => $this->type,
            'image' => $this->image,
            'category_id' => (int)$this->category_id,   
            'item_type' => $this->item_type,                     
            'item_id' => $this->item_type != 'products' ? $this->item_id : json_decode($this->item_id),  
            'item' => $item,
            'category_name' => $category_name
          

        ];
    }
}
