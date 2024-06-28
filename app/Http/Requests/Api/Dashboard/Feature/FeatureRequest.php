<?php

namespace App\Http\Requests\Api\Dashboard\Feature;

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

    public function messages()
    {

        $arr = [];

        $lang = app()->getLocale();

        if($lang == 'en') {

            $arr['main_category_ids.required'] = ' main categories is required';
            $arr['main_category_ids.array'] = ' main categories is must be a array';
            $arr['is_active.required'] = ' is active is required';
            $arr['is_active.in'] = ' is active must be is zero or one';
            $arr['ordering.required'] = ' ordering is required';
            $arr['ordering.unique'] = ' ordering must be unique';
            $arr['values.required'] = ' values is required';
            $arr['values.array'] = ' values must be array';


            if(! empty(request()->main_category_ids)) {
                foreach(request()->main_category_ids as $key => $category_id) {
                    $arr['main_category_ids.'.$key.'.exists'] = ' main category '  .($key + 1). ' is not found';
                    $arr['main_category_ids.'.$key.'.distinct'] = ' main category '  .($key + 1). ' must be distinct';
                }
            }


        } else {

            $arr['main_category_ids.required'] = ' الأقسام الرئيسية مطلوبة ';
            $arr['main_category_ids.array'] = ' الأقسام الرئيسية يجب ان تحتوي علي قيم صحيحة';
            $arr['is_active.required'] = ' حالة التفعيل مطلوبة';
            $arr['is_active.in'] = ' حالة التفعيل يجب ان تكون صفر أو واحد';
            $arr['ordering.required'] = ' الترتيب مطلوب';
            $arr['ordering.unique'] = ' الترتيب لا يجب ان يحتوي علي قيم مسبقة';
            $arr['values.required'] = ' القيم مطلوبة';
            $arr['values.array'] = 'القيم يجب ان تحتوي علي قيم صحيحه';

            if(! empty(request()->main_category_ids)) {
                foreach(request()->main_category_ids as $key => $category_id) {
                    $arr['main_category_ids.'.$key.'.exists'] = ' عفوا القسم الرئيسي '  .($key + 1). ' غير موجود ';
                    $arr['main_category_ids.'.$key.'.distinct'] = ' عفوا القسم الرئيسي '  .($key + 1). ' لا يجب ان يحتوي علي قيم متكررة ';
                }
            }

        }

        foreach(config('translatable.locales') as $locale) {
            if($lang == 'en') {

                $arr[$locale.'.title.string'] = ' title is must be a string';
                $arr[$locale.'.title.required'] = ' title is required';


            } else {
                $msg = $locale == 'en' ? 'باللغة الأنجليزية' : 'باللغة العربية';

                $arr[$locale.'.title.string'] = ' العنوان يجب أن يكون نصا ';
                $arr[$locale.'.title.required'] = ' العنوان مطلوب ';

            }

        }

        return $arr;
    }

}
