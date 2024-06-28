<?php

namespace App\Http\Requests\Api\App\User;

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
        return [
            'phone_code' => 'nullable|string|exists:users,phone_code',
            'country_id' => 'required|required_with:city_id|exists:countries,id',
            'phone' => 'required|numeric|exists:users,phone',
        ];
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
