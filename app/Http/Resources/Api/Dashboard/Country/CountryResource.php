<?php

namespace App\Http\Resources\Api\Dashboard\Country;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            $locales[$locale]['name']        = $this->translate($locale)->name;
            $locales[$locale]['slug']        = $this->translate($locale)->slug;
            $locales[$locale]['currency']    = $this->translate($locale)->currency;
            $locales[$locale]['nationality'] = $this->translate($locale)->nationality;
        }

        return [
            'id'                 => $this->id,
            'continent'          => $this->continent,
            'phone_code'         => $this->phone_code,
            'short_name'         => $this->short_name,
            'phone_number_limit' => (int) $this->phone_number_limit,
            'image'              => $this->image,
            'area'               => $this->area,
            'created_at'         => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ] + $locales;
    }
}
