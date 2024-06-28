<?php

namespace App\Http\Resources\Api\SiteMap;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name_ar' => $this->translate('ar')->name,
            'name_en' => $this->translate('en')->name,
            'slug_ar' => $this->translate('ar')->slug,
            'slug_en' => $this->translate('en')->slug,
        ];
    }
}
