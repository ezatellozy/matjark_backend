<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Support\Str;
use App\Models\User;

class CheckRequest extends ApiMasterRequest
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
        $user = User::where(['phone'=>$this->phone])->firstOrFail();
        if ($user->phone_verified_at || $user->email_verified_at) {
            $code = 'required|exists:users,reset_code';
        }else{
            $code = 'required|exists:users,verified_code';
        }
        return [
            'phone' => 'required|exists:users,phone',
            'code' => $code,
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
