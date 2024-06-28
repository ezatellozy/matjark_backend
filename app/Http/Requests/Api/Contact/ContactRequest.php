<?php

namespace App\Http\Requests\Api\Contact;

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
       
        $fullname = 'required|string|between:2,250';
    
        $phone = 'required|numeric|digits_between:10,23';

        return [

           'fullname' => $fullname,
           'phone' => $phone,
           'country_id' => 'required|exists:countries,id',
           'title'    => 'nullable|string|between:2,250',

           'content'    => 'required|string|between:2,10000',
        ];
    }
}
