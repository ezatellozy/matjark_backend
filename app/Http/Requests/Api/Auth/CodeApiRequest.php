<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Support\Str;

class CodeApiRequest extends ApiMasterRequest
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
            'code' => 'required|exists:users,verified_code',
            'phone' => 'required|numeric|exists:users,phone',
            'device_token' => 'required',
            'type' => 'required|in:ios,android',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'location' => 'nullable|string|between:3,250',
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
