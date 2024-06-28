<?php

namespace App\Http\Requests\Api\Website\General;

use App\Http\Requests\Api\ApiMasterRequest;

class ContactRequest extends ApiMasterRequest
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
            'fullname' => 'nullable|string|between:2,250',
            'email' => 'nullable|email',
            'title'    => 'nullable|string|between:2,250',
            'phone' => 'nullable|numeric|digits_between:10,23',
            "phone_code" => "nullable|exists:countries,phone_code",
            'content'    => 'required|string|between:2,10000',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
    public function getValidatorInstance()
    {
        $data = $this->all();
        $user = auth()->guard('api')->user();
        if ($user  != null) {
            $data['user_id'] = $user->id;
        }
        // if (isset($data['phone']) && $data['phone']) {
        //     $data['phone'] = filter_mobile_number($data['phone']);
        // }
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
