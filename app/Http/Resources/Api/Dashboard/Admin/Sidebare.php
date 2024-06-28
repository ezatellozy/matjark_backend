<?php

namespace App\Http\Resources\Api\Dashboard\Admin\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class Sidebare extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $user =  User::findOrFail(628);
        $user =  auth()->guard('api')->user();
        return [
            "id"             => $this->id,
            "title"          => $this->name,
            "key"           => $this->key,
            "icon"           => $this->icon,
            "permissions"       => PermissionResource::collection($user->role?->permissions()->where("permission_category_id", $this->id)->where("show_in_side_bar", 1)->get())
        ];
    }
}
