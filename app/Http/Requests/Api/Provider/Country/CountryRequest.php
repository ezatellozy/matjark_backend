<?php

namespace App\Http\Requests\Api\Provider\Country;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class CountryRequest extends ApiMasterRequest
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
        $country = isset($this->country) ? Country::findOrFail($this->country) : null;

        try{
            $status = isset($this->country) ? 'nullable' : 'required';
            $rules = [
                'phone_code'         => [$status, 'numeric', 'digits_between:1,3', Rule::unique('countries')->ignore($country)->where(function ($query) { return $query->whereNull('deleted_at');})],
                'short_name'         => 'nullable|string',
                'phone_number_limit' => 'nullable|numeric',
                'continent'          => $status.'|in:africa,europe,asia,south_america,north_america,australia',
                'image'              => $status.'|file',
                'area'               => $status.'|array|min:4',
                'area.*'             => 'array',
                'area.*.lat'         => 'numeric',
                'area.*.lng'         => 'numeric',
            ];

            foreach(config('translatable.locales') as $locale)
            {
                $rules[$locale.'.name'] = $status.'|string|between:2,45';
                $rules[$locale.'.nationality'] = $status.'|string|between:2,45';
                $rules[$locale.'.currency'] = $status.'|string|between:2,45';
                $rules[$locale.'.slug'] = 'nullable|string|between:2,45';
            }

            return $rules;
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard/api.update.fail')], 422));
        }
    }

}
