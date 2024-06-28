<?php

namespace App\Http\Requests\Api\Dashboard\Profile;

use App\Http\Requests\Api\ApiMasterRequest;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends ApiMasterRequest
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
        try{
            $country_id = $this->country_id ?? optional(auth()->guard('api')->user()->country)->id;
            return [
                'first_name'    => 'nullable|string',
                'last_name'     => 'nullable|string',
                'email'         => ['nullable', Rule::unique('users')->ignore(auth('api')->id())->where(fn ($query) => $query->whereNull('deleted_at'))],
                'avatar'        => 'nullable|file',
                'phone_code'    => 'nullable|integer',
                'phone'         => 'nullable|integer',
                'country_id'    => 'nullable|exists:countries,id',
                'city_id'       => 'nullable|exists:countries,id',
            ];
        }catch(Exception $e)
        {
            throw new HttpResponseException(response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard/api.update.fail')], 422));
        }
    }
}
