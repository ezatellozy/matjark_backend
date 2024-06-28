<?php

namespace App\Http\Requests\Api\Dashboard\Role;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
          "permission_ids" =>"required|array" ,
          "permission_ids.*"  => "exists:permissions,id"
        ];
        foreach (config('translatable.locales') as $locale) {
            $rules[$locale . '.name'] = 'required|string|between:3,250';
        }
        return $rules;
    }
}
