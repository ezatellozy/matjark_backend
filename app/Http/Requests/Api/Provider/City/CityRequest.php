<?php

namespace App\Http\Requests\Api\Provider\City;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Validation\Rule;

class CityRequest extends ApiMasterRequest
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
        $status = isset($this->city) ? 'nullable' : 'required';

        $rules = [
            'country_id'     => $status.'|exists:countries,id',
            'short_name'     => 'nullable|string',
            'is_shipping'    => 'nullable|in:0,1',
            'shipping_price' => ['numeric', Rule::requiredIf(function () { return setting('shipping_on') == 'city' && $this->is_shipping;})],
            'postal_code'    => 'nullable|numeric|min:4',
            'area'           => $status.'|array|min:4',
            'area.*'         => 'array',
            'area.*.lat'     => 'numeric',
            'area.*.lng'     => 'numeric',
        ];

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name'] = $status.'|string|between:2,45';
            $rules[$locale.'.slug'] = 'nullable|string|between:2,45';
        }

        return $rules;
    }
}
