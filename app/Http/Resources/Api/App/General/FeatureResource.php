<?php

namespace App\Http\Resources\Api\App\General;

use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
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
            'id'        => (int) $this->id,
            'name'      => (string) $this->name,
            'is_active' => (bool) $this->is_active,
            // 'ordering'  => (int) $this->ordering,
            'values'    => FeatureValueResource::collection($this->values),
        ];
    }
}
