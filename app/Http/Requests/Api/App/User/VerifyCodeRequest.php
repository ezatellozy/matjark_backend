<?php

namespace App\Http\Requests\Api\App\User;

use App\Http\Requests\Api\ApiMasterRequest;

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
        return [
            "phone"      => "required",
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
