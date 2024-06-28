<?php

namespace App\Http\Requests\Api\Website\User;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;

class VerifyCodeRequest extends ApiMasterRequest
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

        if(request()->phone_code != null) {
            $country = Country::where('phone_code',request()->phone_code)->first();
        }

        return [
            "phone"      => "required". ($country != null ? '|digits:'.$country->phone_number_limit : ''),
            "phone_code" => "required|exists:countries,phone_code",
            "code"       => "required"
        ];
    }

    public function getValidatorInstance()
    {
        $data = $this->all();
        if (isset($data['phone']) && $data['phone']) {
            $data['phone'] = validateIfPhoneStartWithZero($data['phone']);
        }
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
