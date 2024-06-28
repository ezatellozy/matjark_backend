<?php

namespace App\Http\Resources\Api\App\Home;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this);
// $catrgory = root($this);
// dd($catrgory);
        return [
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'image' => $this->image,
            'have_layers'  => $this->children()->count() > 0 ?true: false,
            // 'slider' => SliderResource::make($catrgory->sliders()->inRandomOrder()->first()),

        ];
    }
}
