<?php

namespace App\Http\Resources\Api\Website\Home;

use App\Models\Category;
use App\Models\Offer;
use Illuminate\Http\Resources\Json\JsonResource;

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

        $item = null;

        if($this->item_type != 'products') {
            if($this->item_type == 'category') {
                $item = Category::find($this->item_id);
            } else {
                $item = Offer::find($this->item_id);
            }
        }

        return [
            'id' => (int)$this->id,
            'image' => $this->image,
            'name' => (string)$this->name,
            'desc' => (string)$this->desc,
            'category_id' => (int)$this->category_id,
            'item_type' => $this->item_type,                     
            'item_id' => $this->item_type != 'products' ? $this->item_id : json_decode($this->item_id), 
            'item' => $item           

        ];
    }
}
