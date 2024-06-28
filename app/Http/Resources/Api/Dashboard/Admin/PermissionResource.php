<?php

namespace App\Http\Resources\Api\Dashboard\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            "id"              => $this->id,
            "icon"            => $this->icon,
            "title"           => $this->title,
            "url"             => $this->front_route_name,
            "back_route_name" => $this->back_route_name,
        ];
    }
}
