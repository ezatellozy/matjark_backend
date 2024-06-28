<?php

namespace App\Http\Requests\Api\Provider\Color;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Validation\Rule;

class ColorRequest extends ApiMasterRequest
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
        $status = isset($this->color) ? 'nullable' : 'required';

        $rules = [
            'hex'      => 'nullable|string|unique:colors,hex,'.$this->color,
            'ordering' => 'nullable|unique:colors,ordering,'.$this->color,
            'image'    => (isset($this->color) ? 'nullable' : 'required_without:hex' ) . '|file',
        ];

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name'] = $status.'|string|between:2,45|unique:color_translations,name,'.$this->color.',color_id';
        }

        return $rules;
    }
}
