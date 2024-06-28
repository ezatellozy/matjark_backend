<?php

namespace App\Http\Requests\Api\App\User;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;

class RegisterRequest extends ApiMasterRequest
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
            'fullname' => 'required|string|between:3,250',
            'password' => 'required|min:6',
            'email'    => 'nullable|email|unique:users,email,NULL,id,deleted_at,NULL', 
            'phone_code' => 'nullable|string',
            'phone'    => 'required|numeric|unique:users,phone,NULL,id,phone_code,' .  $this->phone_code,
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'country_id' => 'required|required_with:city_id|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'location_description' => 'nullable|string|between:3,250',
            'profile_date' => 'nullable',
        ];
    }

    public function getValidatorInstance()
    {
        $data = $this->all();
        if (isset($data['phone']) && $data['phone']) {
            $data['phone'] = filter_mobile_number($data['phone']);
        }
        $data['phone_code'] = isset($data['country_id']) && Country::find($data['country_id'])!= null ? Country::find($data['country_id'])->phone_code: null;
        $profile_date = [
            'lat' => isset($data['lat']) && $data['lat'] != null ? $data['lat'] : null,
            'lng' => isset($data['lng']) && $data['lng'] != null ? $data['lng'] : null,
            'location_description' => isset($data['location_description']) && $data['location_description'] != null ? $data['location_description'] : null,
            'country_id' => isset($data['country_id']) && $data['country_id'] != null ? $data['country_id'] : null,
            'city_id' => isset($data['city_id']) && $data['city_id'] != null ? $data['city_id'] : null,
        ];
        $data['profile_date'] = $profile_date;
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
