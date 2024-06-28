<?php

namespace App\Http\Resources\Api\Provider\Category;

use App\Http\Resources\Api\Provider\Color\ColorResource;
use App\Http\Resources\Api\Provider\Feature\FeatureResource;
use App\Http\Resources\Api\Provider\Size\SizeResource;
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
