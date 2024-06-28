<?php

namespace App\Http\Resources\Api\Dashboard\Color;

use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
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
        }

        return [
            'id'       => (int) $this->id,
            'name'     => (string) $this->name,
            'hex'      => $this->hex ? (string) $this->hex : null,
            'image'    => (string) $this->image,
            'ordering' => (int) $this->ordering
        ] + $locales;
    }
}
