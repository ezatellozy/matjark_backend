<?php

namespace App\Http\Resources\Api\Provider\Product;

use App\Http\Resources\Api\Provider\Feature\FeatureResource;
use App\Http\Resources\Api\Provider\Feature\FeatureValueResource;
use App\Models\Feature;
use App\Models\FeatureValue;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $feature = Feature::find($this['feature_id']);
        $value   = FeatureValue::find($this['value_id']);
        $values  = FeatureValue::where('feature_id', $this['feature_id'])->get();

        return [
            'feature' => $feature ? ['id' => $feature->id, 'name' => $feature->name] : null ,
            'value'   => $value ? ['id'   => $value->id, 'value' => $value->value] : null,
            'values'  => FeatureValueResource::collection($values),
        ];
    }
}
