<?php

namespace App\Http\Requests\Api\Provider\Size;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;

class SizeRequest extends ApiMasterRequest
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
        $status = isset($this->size) ? 'nullable' : 'required';

        $rules = [
            'main_category_ids'   => $status.'|array',
            'main_category_ids.*' => 'exists:categories,id|distinct', // function ($attribute, $value, $fail) { if (Category::find($value) and Category::find($value)->position != 'main') {$fail(trans('dashboard.error.category_id_not_true'));}}
            'ordering'            => 'nullable|unique:sizes,ordering,'.$this->size,
        ];

        foreach(config('translatable.locales') as $locale)
        {
            // $rules[$locale.'.name'] = $status.'|string|between:1,45|unique:size_translations,name,'.$this->size.',size_id';
            $rules[$locale.'.name'] = $status.'|string|between:1,45';
            $rules[$locale.'.desc'] = 'nullable|string|min:3|max:10000';
            $rules[$locale.'.tag']  = 'required|string|between:1,45';
        }

        return $rules;
    }
}
