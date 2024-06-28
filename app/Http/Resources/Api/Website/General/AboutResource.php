<?php

namespace App\Http\Resources\Api\Website\General;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\Meta\MetaResource;
use App\Models\Setting;

class AboutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $meta_data = [
            'id'                 => @$this->id,
            'meta_tag'           => @$this->metas->meta_tag,
            'meta_title'         => @$this->metas->meta_title,
            'meta_description'   => @$this->metas->meta_description,
            'meta_canonical_tag' => asset('storage/images/setting').'/'.Setting::where('key', "website_logo")->first()->value,
        ];

        return [
            'id'  => (int)$this->id,
            'title' => (string)$this->title,
            'desc' => (string)$this->desc,
            'image'                   => $this->image,
            'created_at'              => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            // 'meta_tags' => MetaResource::make($this->metas)
            'meta_data' => $meta_data
        ];
    }
}
