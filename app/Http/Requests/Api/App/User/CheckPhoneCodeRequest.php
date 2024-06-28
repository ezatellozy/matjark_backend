<?php

namespace App\Http\Requests\Api\App\User;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckPhoneCodeRequest extends ApiMasterRequest
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
            $data = [
                'code' =>'required',
              
            ];
            $user = auth()->guard('api')->user();
            if ($user->verified_code != $this->code) {
                throw new HttpResponseException(response()->json([
                    'status' => 'fail',
                    'message' =>  trans('app.auth.code_not_true'),
                    'data' => null,
                ], 422));
            }
            return $data;  
    }
}
