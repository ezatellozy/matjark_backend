<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Support\Str;
use App\Models\{User};

class SignUpRequest extends ApiMasterRequest
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
            'phone'    => 'required|numeric|digits_between:5,20|starts_with:9665,05|unique:users,phone',
            'gender'   => 'nullable|in:male,female',

            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'country_id' => 'nullable|required_with:city_id|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',



            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'location_description' => 'required|string|between:3,250',


        ];
    }

    public function getValidatorInstance()
    {
       $data = $this->all();
       if (isset($data['phone']) && $data['phone']) {
           $data['phone'] = filter_mobile_number($data['phone']);
       }



       $this->getInputSource()->replace($data);
       return parent::getValidatorInstance();
    }

}
