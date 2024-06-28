<?php

namespace App\Http\Requests\Api\Provider\Profile;

use App\Http\Requests\Api\ApiMasterRequest;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends ApiMasterRequest
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
        try {
            return [
                'old_password' => ['required', 'min:6', function ($attribute, $value, $fail) {
                    if (! Hash::check($value, auth()->guard('api')->user()->password)) {
                        $fail(trans('dashboard.profile.old_password_is_not_correct'));
                    }
                }],
                'password' => 'required|min:6|confirmed',
            ];
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.error.fail')], 422));
        }
    }
}
