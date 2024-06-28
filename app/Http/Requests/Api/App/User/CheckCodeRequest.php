<?php

namespace App\Http\Requests\Api\App\User;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CheckCodeRequest extends ApiMasterRequest
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
        $user =  User::where(['phone'=>$this->phone])->firstOrFail();
        if ($user->phone_verified_at || $user->email_verified_at) {
            $code = 'required|exists:users,reset_code';
        }else{
            $code = 'required|exists:users,verified_code';
        }
        return [
            'country_id' => 'required|exists:countries,id,deleted_at,NULL',
            'phone_code' => 'nullable|string',
            'phone' => 'required|numeric|exists:users,phone,phone_code,' .  $this->phone_code,
            'code' => $code,
        ];
    }

    public function getValidatorInstance()
    {
       $data = $this->all();
       if (isset($data['phone']) && $data['phone']) {
           $data['phone'] = filter_mobile_number($data['phone']);
       }
       $data['phone_code'] = isset($data['country_id']) && Country::find( $data['country_id'] )!= null ? Country::find( $data['country_id'] )->phone_code: null;

       $this->getInputSource()->replace($data);
       return parent::getValidatorInstance();
    }
}
