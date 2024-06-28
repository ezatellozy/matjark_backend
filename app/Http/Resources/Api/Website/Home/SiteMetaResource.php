<?php

namespace App\Http\Resources\Api\Website\Home;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteMetaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $locale = $request->header('Accept-Language') ? app()->getLocale($request->header('Accept-Language')) : 'ar';
        return [
            'site_meta_tag' => $this->where('key', "site_meta_tag_$locale")->first()->key,
            'site_meta_title' => $this->where('key', "site_meta_title_$locale")->first()->key,
            'site_meta_description' => $this->where('key', "site_meta_description_$locale")->first()->key,
        ];
    }
}
