<?php

namespace App\Http\Requests\Api\Provider\Slider;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;

class SliderRequest extends ApiMasterRequest
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
        $status = isset($this->slider) ? 'nullable' : 'required';

        $rules = [
            'ordering'   => $status . '|integer',
            'is_active'  => 'nullable|in:0,1',
            'type'     => 'nullable|in:divided,main,banner',
            'platform' => 'nullable|in:app,website,all',
            // 'link'       => 'nullable|url',
            'category_id' =>  $status . '|exists:categories,id', //function ($attribute, $value, $fail) { if (Category::find($value) and Category::find($value)->position != 'main') {$fail(trans('dashboard.error.category_id_not_true'));}},
        ];

        // if ($this->link and $this->category_id) {
        //     throw new HttpResponseException(response()->json([
        //         'status' => 'fail',
        //         'data' => null,
        //         'message' => 'يجب اضافة اما رابط او فئه',
        //     ], 422));
        // }

        foreach (config('translatable.locales') as $locale) {
            $rules[$locale . '.name']      = $status . '|string';
            // $rules[$locale.'.link_name'] = $status.'|string';
            $rules[$locale . '.desc']      = 'nullable|string';
            $rules[$locale . '.slug']      = 'nullable|string';
            $rules[$locale . '.image']     = $status . '|file';
        }

        return $rules;
    }
}
