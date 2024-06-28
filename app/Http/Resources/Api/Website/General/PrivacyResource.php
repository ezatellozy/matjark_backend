<?php

namespace App\Http\Resources\Api\Website\General;

use Illuminate\Http\Resources\Json\JsonResource;

class PrivacyResource extends JsonResource
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
            'id'  => (int)$this->id,
            'title' => (string)$this->title,
            'desc' => (string)$this->desc,
            'image'                   => $this->image,
            'created_at'              => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
