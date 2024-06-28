<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Support\Str;

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
        return [
          'username' => 'required',
          'password' => 'required',
          'device_token' => 'required',
          'type' => 'required|in:ios,android,huawei',
          'lat' => 'nullable|numeric',
          'lng' => 'nullable|numeric',
          'location' => 'nullable|string|between:3,250',
        ];
    }

    public function getValidatorInstance()
    {
       $data = $this->all();
       if (isset($data['username']) && $data['username']) {
           $data['username'] = filter_mobile_number($data['username']);
       }
       $this->getInputSource()->replace($data);
       return parent::getValidatorInstance();
    }

}
