<?php

namespace App\Http\Requests\Api\Provider\Auth;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends ApiMasterRequest
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
            'email' => [
                'required',
                Rule::exists('users')->where(function ($query) {
                    return $query->whereIn('user_type', ['provider']);
                }),
            ],
            'password' => 'required'
        ];
    }
}
