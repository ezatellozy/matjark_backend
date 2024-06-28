<?php

namespace App\Http\Requests\Api\Provider\Feature;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;

class FeatureRequest extends ApiMasterRequest
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
        $status = isset($this->feature) ? 'nullable' : 'required';

        $rules = [
            'main_category_ids'   => $status.'|array',
            'main_category_ids.*' => 'exists:categories,id|distinct', // function ($attribute, $value, $fail) { if (Category::find($value) and Category::find($value)->position != 'main') {$fail(trans('dashboard.error.category_id_not_true'));}}
            'is_active'           => $status.'|in:0,1',
            'ordering'            => 'nullable|unique:features,ordering,'.$this->feature,
            'values'              => 'required|array',
        ];

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name']   = 'required|string|between:2,45|unique:feature_translations,name,'.$this->feature.',feature_id';
            $rules['values.*.'.$locale.'.value'] = 'required|string';
        }

        return $rules;
    }
}
