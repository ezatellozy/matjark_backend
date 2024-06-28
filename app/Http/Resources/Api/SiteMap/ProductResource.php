<?php

namespace App\Http\Resources\Api\SiteMap;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $translations = $this->translations;
        foreach($translations as $translation){
            $locale = $translation->locale;
            $url = ($locale == 'ar')? 'products/'.$this->translate('ar')->slug: 'en/products/'.$this->translate('en')->slug;
        }
        return [
            'id' => $this->id,
            'url' => $url,
            'lastmod' => $this->updated_at,
            'changefreq' => 'daily'
            // 'name_en' => $this->translate('en')->name,
            // 'slug_ar' => $this->translate('ar')->slug,
            // 'slug_en' => $this->translate('en')->slug,
        ];
    }
}
