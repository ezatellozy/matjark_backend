<?php

namespace App\Http\Requests\Api\Dashboard\Category;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CategoryRequest extends ApiMasterRequest
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
        $status = isset($this->category) ? 'nullable' : 'required';

        $id = isset($this->category)? $this->category: '';

        $category = isset($this->category)? Category::find($this->category):null;
        
        $meta_id = (isset($this->category) && ($category->metas()->count()))?$category->metas()->first()->id:null;

        $rules = [
            'is_active' => 'nullable|in:0,1',
            'parent_id' => 'nullable|exists:categories,id',
            'ordering'  => 'nullable|numeric|unique:categories,ordering,'. $this->category,
            'image'     => 'nullable|file',
            'meta_canonical_tag' => 'nullable|url'
        ];

        $parent = isset($this->parent_id) ? Category::find($this->parent_id) : null;

        // if ($parent and (! in_array($parent->position, ['main', 'first_sub']) or $this->parent_id == $this->category))
        // {
        //     throw new HttpResponseException(response()->json([
        //         'status' => 'fail',
        //         'data' => null,
        //         'message' => trans('dashboard.error.parent_id_not_true'),
        //     ], 422));
        // }
        // return dd($this->category);
        if ($this->category && Category::find($this->category)->position == 'main' && $this->parent_id && (Category::find($this->parent_id)->position != 'main' or root($this->parent_id) == $this->category)
        )
        {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => trans('dashboard.error.you_cant_do_that'),
            ], 422));
        }

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name'] = $status.'|string';
            // $rules[$locale.'.keywords'] = $status.'|string';

            // $rules[$locale.'.slug'] = [$status, 'string', Rule::unique('category_translations', 'slug')->ignore($id, 'category_id')
            // // ->where(function ($query) use($id) {
            // //     $categories = Category::whereNull('deleted_at')->where('is_active', 1)->get();
            // //     return dd($categories);
            // //     return dd($id);
            // //     return $query->where('categories.deleted_at', null);
            // // })
            // ];
            // $rules[$locale.'.slug'] = $status.'|string|unique:category_translations,slug,' . $id . ',category_id|between:2,45';
            // $rules[$locale.'.desc'] = 'nullable|string|between:2,10000';
            $rules[$locale.'.desc'] = 'nullable|string';
            // $rules[$locale . '.meta_tag'] = $status . '|string|unique:meta_translations,meta_tag|between:2,45';
            // $rules[$locale . '.meta_title'] = $status.'|string|unique:meta_translations,meta_title|between:2,255';

            $rules[$locale . '.meta_tag'] = [$status,'string'];
            $rules[$locale . '.meta_title'] = [$status, 'string'];
            $rules[$locale . '.meta_description'] = $status.'|string';

            $rules[$locale . '.meta_canonical_tag'] = 'nullable';

            // $rules[$locale.'.slug'] = $status.'|string|unique:category_translations,slug,' . $id . ',category_id|between:2,45';

            // $categoriesArr = CategoryTranslation::where('slug',$id)->pluck('category_id')->toArray();

            $CategoryTranslationArr = CategoryTranslation::where('slug',request()->slug)->whereHas('category', function ($query) {
                $query->withTrashed(); // Include soft deleted categories
                // $query->whereNotNull('deleted_at'); // Ensure the categories are soft deleted
            })->pluck('id')->toArray();

            info($CategoryTranslationArr);
            
            $rules[$locale.'.slug'] =[
                $status,
                Rule::unique('category_translations', 'slug')->where(function ($query) use($id,$CategoryTranslationArr) {
                    return $query->where('category_id', '!=', $id)->whereNotIn('id', $CategoryTranslationArr);
                })
            ];

        }
        return $rules;
    }





}
