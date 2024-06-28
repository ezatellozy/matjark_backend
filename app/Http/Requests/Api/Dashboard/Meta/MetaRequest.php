<?php

namespace App\Http\Requests\Api\Dashboard\Meta;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Meta;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class MetaRequest extends ApiMasterRequest
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
        $status = $this->meta? 'nullable': 'required';
        $rules = [
            'model_type' => 'required|in:Category,Product',
            'model_id' => 'required'
            ];
        foreach (config('translatable.locales') as $locale) {
            $rules[$locale . '.tag'] = $status . '|string|unique:meta_translations,tag|between:2,45';
            $rules[$locale . '.title'] = $status.'|string|unique:meta_translations,title|between:2,255';
            $rules[$locale . '.description'] = $status.'|string';
        }

        return $rules;
    }
}