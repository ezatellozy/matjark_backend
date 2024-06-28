<?php

namespace App\Http\Resources\Api\Dashboard\Category;

use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Feature\FeatureResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
use App\Models\Color;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $colors = Color::get();
        
        return [
            'colors'   => ColorResource::collection($colors),
            'sizes'    => SizeResource::collection(getCategorySizes($this->resource)),
            'features' => FeatureResource::collection(getCategoryFeatures($this->resource))
        ];
    }
}
