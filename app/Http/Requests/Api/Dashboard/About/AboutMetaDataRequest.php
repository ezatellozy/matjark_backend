<?php

namespace App\Http\Requests\Api\Dashboard\About;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Validation\Rule;

class AboutMetaDataRequest extends ApiMasterRequest
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
        $status = isset($this->about_meta_data) ? 'nullable' : 'required';

        $rules = [
            'meta_canonical_tag' => 'nullable|url'
        ];

        foreach(config('translatable.locales') as $locale){
             $rules[$locale . '.meta_tag'] = [$status,'string', 'between:2,45'];
            $rules[$locale . '.meta_title'] = [$status, 'string', 'between:2,255'];
            $rules[$locale . '.meta_description'] = $status.'|string';
        }
        return $rules;
    }
}