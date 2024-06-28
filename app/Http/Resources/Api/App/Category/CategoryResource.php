<?php

namespace App\Http\Resources\Api\App\Category;

use App\Http\Resources\Api\App\Home\SliderResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => (string)$this->name,
            'image' => $this->image,
            'slider' => SliderResource::collection($this->sliders()->inRandomOrder()->take(2)->get()),
            'sub_categories' =>  CategoryResource::collection($this->children),
        ];
    }
}
