<?php

namespace App\Http\Resources\Api\Dashboard\Feature;

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
        $locales = [];

        foreach (config('translatable.locales') as $locale) {
            $locales[$locale]['value'] = $this->translate($locale)->value;
        }

        return [
            'id'    => (int) $this->id,
            'value' => (string) $this->value,
        ] + $locales;
    }
}
