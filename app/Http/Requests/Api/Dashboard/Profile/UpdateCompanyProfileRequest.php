<?php

namespace App\Http\Requests\Api\Dashboard\Profile;

use App\Http\Requests\Api\ApiMasterRequest;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCompanyProfileRequest extends ApiMasterRequest
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
            return [
                'name'       => 'required',
                'tax_number' => 'required',
                'logo'       => 'nullable|image',
            ];
        }catch(Exception $e)
        {
            throw new HttpResponseException(response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard/api.update.fail')], 422));
        }
    }
}
