<?php

namespace App\Http\Requests\Api\Website\User;

use App\Http\Requests\Api\ApiMasterRequest;

class UpdatePasswordRequest extends ApiMasterRequest
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
        return [
            'old_password' => ['required', 'min:6', function ($attribute, $value, $fail) {
                if (!\Hash::check($value, auth()->guard('api')->user()->password)) {
                    $fail(trans('app.auth.the_old_password_is_incorrect'));
                }
            }],
            'password' => 'required|min:6',
        ];
    }
}
