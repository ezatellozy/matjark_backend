<?php

namespace App\Http\Resources\Api\App\Product;

use App\Models\{Feature, FeatureValue};
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFeaturesResource extends JsonResource
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
        return [
            'feature' => $feature ? ['id' => (int)$feature->id, 'name' => (string)$feature->name] : null ,
            'value'   => $value ? ['id'   => (int)$value->id, 'name' => (string)$value->value] : null
        ];
    }
}
