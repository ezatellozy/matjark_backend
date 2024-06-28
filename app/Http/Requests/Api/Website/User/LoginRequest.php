<?php

namespace App\Http\Requests\Api\Website\User;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;

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

        $country = null;

        if($this->get('phone_code') != null) {
            $country = Country::where('phone_code',$this->get('phone_code'))->first();
        }

        $identifier_type = $this->identifier_type;
        ///$identifier_type = 'email';

        if ($identifier_type == 'phone') {
            $identifier_validation = 'required|numeric|exists:users,phone'. ($country != null ? '|digits:'.$country->phone_number_limit : '');
            $countryValidation = 'required|exists:countries,id,deleted_at,NULL' ;
        } else {
            $identifier_validation = 'required|email|exists:users,email';
            $countryValidation = 'nullable|exists:countries,id,deleted_at,NULL' ;

        }
        return [
            'country_id' =>      $countryValidation ,
            'phone_code' => 'nullable|exists:users,phone_code',
            'phone' =>       $identifier_validation,
            'password' => 'required',
            'device_token' => 'required',
            'type' => 'required|in:ios,android,huawei',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'location_description' => 'nullable|string|between:3,250',
            // 'guest_token' => 'nullable|exists:carts,guest_token',
            'guest_token' => 'nullable',


        ];
    }

    public function getValidatorInstance()
    {
        $data = $this->all();
        if (isset($data['phone']) && $data['phone']) {
            switch ($data['phone']) {
                // case filter_var($data['phone'], FILTER_VALIDATE_EMAIL):
                //     $data['identifier_type'] = 'email';
                //     break;
                case is_numeric($data['phone']):
                    $data['phone'] = filter_mobile_number($data['phone']);
                    $data['identifier_type'] = 'phone';
                    break;
                default:
                    $data['phone'] = filter_mobile_number($data['phone']);

                    $data['identifier_type'] = 'phone';
                    break;
            }
        }
        $data['phone_code'] = isset($data['country_id']) && Country::find($data['country_id']) != null ?   Country::find($data['country_id'])->phone_code : null;

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
