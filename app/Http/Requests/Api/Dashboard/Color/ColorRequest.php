<?php

namespace App\Http\Requests\Api\Dashboard\Color;

use App\Http\Requests\Api\ApiMasterRequest;
use Illuminate\Validation\Rule;
use App\Models\Color;

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
        $hex_values = Color::pluck('hex');
        $hex_arrray = array_filter($hex_values->toArray());

        $rules = [
            // 'hex'      => 'nullable|string|unique:colors,hex,'.$this->color,
            'hex'      => ['nullable','string',Rule::unique('colors','hex')->ignore($this->color)->whereIn('hex', $hex_arrray)],
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
