<?php

namespace App\Http\Requests\Api\Dashboard\Permission;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends ApiMasterRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'icon'                      => 'nullable|string',
            'front_route_name'          => 'required|string',
        ];

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.title'] = 'required|string|between:2,100'; // |regex:/([\p{Arabic}a-zA-Z0-9]+)/
        }

        return $rules;
    }
}
