<?php

namespace App\Http\Resources\Api\Dashboard\District;

use App\Http\Resources\Api\Dashboard\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
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
            $locales[$locale]['name'] = $this->translate($locale)->name;
            $locales[$locale]['slug'] = $this->translate($locale)->slug;
        }

        return [
            'id'                      => $this->id,
            'uuid'                    => $this->uuid,
            'location'                => $this->location,
            'area'                    => $this->area,
            'city'                    => CityResource::make($this->city),
            'is_available_for_orders' => (bool)$this->is_available_for_orders,
            'created_at'              => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ]+$locales;
    }
}
