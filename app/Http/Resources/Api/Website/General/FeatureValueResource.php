<?php

namespace App\Http\Resources\Api\Website\General;

use Illuminate\Http\Resources\Json\JsonResource;

class FeatureValueResource extends JsonResource
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
            'id'    => (int) $this->id,
            'value' => (string) $this->value,
            'feature_id'        => (int) $this->feature->id,

        ];
    }
}
