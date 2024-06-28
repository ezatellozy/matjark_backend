<?php

namespace App\Http\Requests\Api\Website\User;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\{Country,User};
use Illuminate\Http\Exceptions\HttpResponseException;

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
        $lang = app()->getLocale();

        // $user =  User::where(['email'=>$this->phone])->first();

        $user = User::where(['phone'=>$this->phone , 'phone_code' => $this->phone_code])->where('user_type','client')->first();

        if ($user == null) {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'message' => $lang == 'en' ? 'sorry user not found' : 'عفوا هذا المستخدم غير موجود',
                'data' => null,
            ], 422));
        }
        
        if ($user->phone_verified_at || $user->email_verified_at) {
            $code = 'required|exists:users,reset_code';
        }else{
            $code = 'required|exists:users,verified_code';
        }

        $identifier_type = $this->identifier_type;
        ///$identifier_type = 'email';

        if ($identifier_type == 'phone') {
            $identifier_validation = 'required|numeric|digits_between:5,20|exists:users,phone';
            $countryValidation = 'required|exists:countries,id,deleted_at,NULL' ;
        } else {
            $identifier_validation = 'required|email|exists:users,email';
            $countryValidation = 'nullable|exists:countries,id,deleted_at,NULL' ;

        }

        return [
            // 'country_id' => 'required|exists:countries,id,deleted_at,NULL',
            // 'phone_code' => 'nullable|string',
            // 'phone' => 'required|numeric|digits_between:5,20|exists:users,phone,phone_code,' .  $this->phone_code,

            'country_id' =>      $countryValidation ,
            'phone_code' => 'nullable|exists:users,phone_code',
            'phone' =>       $identifier_validation,

            'code' => $code,
        ];
    }

    public function getValidatorInstance()
    {
       $data = $this->all();

    //    if (isset($data['phone']) && $data['phone']) {
    //        $data['phone'] = filter_mobile_number($data['phone']);
    //    }
    //    $data['phone_code'] = isset($data['country_id']) && Country::find( $data['country_id'] ) != null ? Country::find( $data['country_id'] )->phone_code: null;

        if (isset($data['phone']) && $data['phone']) {
            switch ($data['phone']) {
                case filter_var($data['phone'], FILTER_VALIDATE_EMAIL):
                    $data['identifier_type'] = 'email';
                    break;
                case is_numeric($data['phone']):
                    $data['phone'] = filter_mobile_number($data['phone']);
                    $data['identifier_type'] = 'phone';
                    break;
                default:
                    $data['phone'] = filter_mobile_number($data['phone']);

                    $data['identifier_type'] = 'phone';
                    break;
            }
        }
        $data['phone_code'] = isset($data['country_id']) && Country::find($data['country_id']) != null ?   Country::find($data['country_id'])->phone_code : null;

       $this->getInputSource()->replace($data);
       return parent::getValidatorInstance();
    }
}
