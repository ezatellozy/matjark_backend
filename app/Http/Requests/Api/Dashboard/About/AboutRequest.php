<?php

namespace App\Http\Requests\Api\Dashboard\About;

use App\Http\Requests\Api\ApiMasterRequest;

class AboutRequest extends ApiMasterRequest
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
        $status = isset($this->about) ? 'nullable' : 'required';

        $rules = [
            'ordering'                => 'nullable|numeric|unique:abouts,ordering,'. $this->about,
            'meta_canonical_tag' => 'nullable'
        ];

        foreach(config('translatable.locales') as $locale){
            $rules[$locale.'.title'] = $status.'|string';
            $rules[$locale.'.desc']  = $status.'|string';
            $rules[$locale.'.slug']  = 'nullable|string';
            $rules[$locale.'.image'] = 'nullable|file';
            $rules[$locale . '.meta_tag'] = $status . '|string|unique:meta_translations,meta_tag';
            $rules[$locale . '.meta_title'] = $status.'|string|unique:meta_translations,meta_title';
            $rules[$locale . '.meta_description'] = $status.'|string';
        }
        return $rules;
    }
}
