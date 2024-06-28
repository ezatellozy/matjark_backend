<?php

namespace App\Http\Resources\Api\Dashboard\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionIndexResource extends JsonResource
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
            "id"                        => (int) $this->id,
            "back_route_name"           => (string) $this->back_route_name,
            "front_route_name"          => (string) $this->front_route_name,
            "icon"                      => (string) $this->icon,
            // "title"                     => (string) trans('dashboard.permissions.'.strtolower($this->title)),
            "title"                     => (string) str_replace(".","_",strtolower($this->title)),

        ];
    }
}
