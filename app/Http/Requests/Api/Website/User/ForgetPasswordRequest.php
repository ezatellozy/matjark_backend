<?php

namespace App\Http\Requests\Api\Website\User;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;

class ForgetPasswordRequest extends ApiMasterRequest
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

        if($this->get('country_id') != null) {
            $country = Country::find($this->get('country_id'));
        }

        $validate_arr = [
            'phone_code' => 'nullable|string|exists:users,phone_code',
            'country_id' => 'required|required_with:city_id|exists:countries,id',
            //'phone' => 'required|numeric|digits_between:5,20|exists:users,phone',
        ];

        
        if($country != null) {
            $validate_arr['phone'] = 'required|numeric|exists:users,phone|digits:'.$country->phone_number_limit;
        } else {
            $validate_arr['phone'] = 'required|numeric|exists:users,phone';
        }

        return $validate_arr;
    }

    public function getValidatorInstance()
    {
        $data = $this->all();
        if (isset($data['phone']) && $data['phone']) {
            $data['phone'] = filter_mobile_number($data['phone']);
        }
        $data['phone_code'] = isset($data['country_id']) && Country::find( $data['country_id'] ) != null ? Country::find($data['country_id'])->phone_code: null;

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
