<?php

namespace App\Http\Requests\Api\Website\User;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;

class UpdatePhoneRequest extends ApiMasterRequest
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

        $country = null;

        if($this->get('phone_code') != null) {
            $country = Country::where('phone_code',$this->get('phone_code'))->first();
        }
        
        return [
            'country_id' => 'required|required_with:city_id|exists:countries,id',
            'phone_code' => 'nullable|string',
            'phone'    => 'required|numeric|unique:users,phone,'.$user.',id,phone_code,' .  $this->phone_code. ($country != null ? '|digits:'.$country->phone_number_limit : ''),   
            'code' =>'required|exists:users,verified_code',
        ];
    }
    public function getValidatorInstance()
    {
       $data = $this->all();
       if (isset($data['phone']) && $data['phone']) {
           $data['phone'] = filter_mobile_number($data['phone']);
       }
       $data['phone_code'] = isset($data['country_id']) && Country::find( $data['country_id'] ) != null ? Country::find( $data['country_id'] )->phone_code: null;

       $this->getInputSource()->replace($data);
       return parent::getValidatorInstance();
    }
}
