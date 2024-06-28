<?php

namespace App\Http\Requests\Api\Website\Address;

use App\Http\Requests\Api\ApiMasterRequest;

class AddressRequest extends ApiMasterRequest
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
        $is_required = 'required';
        if ($this->route('address')) {
            $is_required = 'nullable';
 
        }
        // dd($this->route('address'));
        $data = [
            'name' =>  $is_required . '|string|max:255',
            'lat' =>  $is_required . '|numeric',
            'lng' =>  $is_required . '|numeric',
            'location_description' => 'nullable|string|max:255',
            'is_default' =>  $is_required . '|in:0,1',
            'full_name' =>  $is_required . '|string|max:255',
            'country_id' =>  $is_required . '|exists:countries,id',
            'city_id' =>  $is_required . '|exists:cities,id',
            'postal_code' =>   'nullable|string|max:30',
            'phone_code' =>  $is_required . '|exists:countries,phone_code',
            'phone' => $is_required . '|numeric|digits_between:5,20',
            'desc' => 'nullable|string|max:255',
            'district' =>  $is_required . '|string|max:255',

        ];
        return $data;
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
