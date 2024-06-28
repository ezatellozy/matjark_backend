<?php

namespace App\Http\Resources\Api\Dashboard\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionSideBarResource extends JsonResource
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
            "title"        => (string) $this->title,
            "url"          => (string) $this->front_route_name,

        ];
    }
}
