<?php

namespace App\Http\Requests\Api\Dashboard\Permission;

use App\Models\{Country, User};
use Illuminate\Validation\Rule;
use App\Services\PhoneNumberService;
use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAllPermissionsRequest extends ApiMasterRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $rules = [
            'permissions.*.id'                      => 'required|exists:permissions,id|distinct',
            'permissions.*.icon'                    => 'nullable|string',
            'permissions.*.front_route_name'        => 'required|string',

        ];

        foreach(config('translatable.locales') as $locale)
        {
            $rules[ 'permissions.*.'.$locale.'.title'] = 'required|string|between:2,100'; // |regex:/([\p{Arabic}a-zA-Z0-9]+)/
        }

        return $rules;
    }

}
