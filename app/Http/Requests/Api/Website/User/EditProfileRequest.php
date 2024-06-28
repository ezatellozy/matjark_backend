<?php

namespace App\Http\Requests\Api\Website\User;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;

class EditProfileRequest extends    ApiMasterRequest
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
        $user = auth('api')->id();
        return [
            'fullname' => 'nullable|string|between:3,250',
            'email'    => 'nullable|email|unique:users,email,' . $user,
            // 'phone_code' => 'nullable|string',
            // 'phone'    => 'nullable|numeric|digits_between:10,23|unique:users,phone,' . $user . ',id,phone_code,' .  $this->phone_code,
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'country_id' => 'nullable|exists:countries,id,deleted_at,NULL',
            'city_id' => 'nullable|exists:cities,id,deleted_at,NULL',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'location_description' => 'nullable|string|between:3,250',
        ];
    }


    // public function getValidatorInstance()
    // {
    //     $data = $this->all();
    //     if (isset($data['phone']) && $data['phone']) {
    //         $data['phone'] = filter_mobile_number($data['phone']);
    //     }
    //     $data['phone_code'] = isset($data['country_id']) && Country::find($data['country_id']) != null ? Country::find($data['country_id'])->phone_code : null;

    //     $this->getInputSource()->replace($data);
    //     return parent::getValidatorInstance();
    // }
}
