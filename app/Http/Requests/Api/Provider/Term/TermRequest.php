<?php

namespace App\Http\Requests\Api\Provider\Term;

use App\Http\Requests\Api\ApiMasterRequest;

class TermRequest extends ApiMasterRequest
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
        $status = isset($this->term) ? 'nullable' : 'required';
        $rules = [
            'ordering'  => 'required|integer'
        ];
        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.title'] = $status.'|string';
            $rules[$locale.'.desc']  = 'nullable|string';
            $rules[$locale.'.slug']  = 'nullable|string';
            $rules[$locale.'.image'] = 'nullable|file';
        }
        return $rules;
    }
}
