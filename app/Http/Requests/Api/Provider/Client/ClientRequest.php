<?php

namespace App\Http\Requests\Api\Provider\Client;

use App\Http\Requests\Api\ApiMasterRequest;

class ClientRequest extends ApiMasterRequest
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
        $status = isset($this->client) ? 'nullable' : 'required';

        return [
            "avatar"     => "nullable|file",
            "fullname"   => $status . "|string",
            "country_id" => "nullable|exists:countries,id",
            "city_id"    => "nullable|exists:cities,id",
            "gender"     => $status . "|in:male,female",
            "password"   => $status . "|min:6|confirmed",
            "phone_code" => $status . "|exists:countries,phone_code",
            "phone"      => $status . "|numeric|unique:users,phone,".$this->client,
            "email"      => "nullable|email|unique:users,email,".$this->client,
            "is_active"  => $status . "|in:0,1",
            "is_ban"     => $status . "|in:0,1",
            "ban_reason" => "nullable|string|required_if:is_ban,1",
        ];
    }
}
