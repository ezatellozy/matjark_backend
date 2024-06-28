<?php

namespace App\Http\Resources\Api\Dashboard\Admin;

use App\Http\Resources\Api\Dashboard\City\CitySimpleResource;
use App\Http\Resources\Api\Dashboard\Country\CountrySimpleResource;
use App\Http\Resources\Api\Dashboard\Role\RoleResource;
use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Permission\PermissionResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $user = auth('api')->user();

        if($user->user_type == 'superadmin') {
            $permissions  =  Permission::get();
        } else {
            $permissions  =  $this->role ? $this->role->permissions()->get() : [];
        }

        return [
            'id'            => $this->id,
            'fullname'      => $this->fullname,
            'avatar'        => @$this->avatar,
            'phone_code'    => (int)$this->phone_code,
            'phone'         => (int)$this->phone,
            'email'         => $this->email,
            'user_type'     => $this->user_type,
            'gender'        => $this->gender,
            'gender_trans'  => trans('app.gender.'.$this->gender),
            'is_active'     => (bool)$this->is_active,
            'is_ban'        => (bool)$this->is_ban,
            'ban_reason'    => $this->ban_reason,
            'country'       => CountrySimpleResource::make($this->country),
            'city'          => CitySimpleResource::make($this->city),
            'token'         => $this->when($this->token, $this->token),
            'created_at'    => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'role'          => RoleResource::make($this->role),
            'role_id'       => $this->role_id,
            'permissions'   => PermissionResource::collection($permissions)


        ];
    }
}
