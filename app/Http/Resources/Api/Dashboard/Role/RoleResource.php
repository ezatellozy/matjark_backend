<?php

namespace App\Http\Resources\Api\Dashboard\Role;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Permission\PermissionResource;

class RoleResource extends JsonResource
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
            "id"            =>  $this->id,
            "name"           =>  $this->name,
            "permission"       => PermissionResource::collection($this->permissions)
        ];
    }
}
