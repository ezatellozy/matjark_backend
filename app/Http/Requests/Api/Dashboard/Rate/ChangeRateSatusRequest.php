<?php

namespace App\Http\Requests\Api\Dashboard\Rate;

use App\Http\Requests\Api\ApiMasterRequest;

class ChangeRateSatusRequest extends ApiMasterRequest
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
            'apply_on'      => 'nullable',
            'status'        => 'required|in:accepted,rejected',
            'reject_reason' => 'nullable',
        ];
    }
}
