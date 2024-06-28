<?php

namespace App\Http\Requests\Api\Provider\Category;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;

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

        $rules = [
            'is_active' => 'nullable|in:0,1',
            'parent_id' => 'nullable|exists:categories,id',
            'ordering'  => 'nullable|numeric|unique:categories,ordering,'. $this->category,
            'image'     => 'nullable|file',
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
        
        if ($this->category && Category::find($this->category)->position == 'main' && $this->parent_id && (Category::find($this->parent_id)->position != 'main' or root($this->parent_id) == $this->category))
        {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => trans('dashboard.error.you_cant_do_that'),
            ], 422));
        }

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name'] = $status.'|string|between:2,45';
            $rules[$locale.'.slug'] = 'nullable|string|between:2,45';
            $rules[$locale.'.desc'] = 'nullable|string|between:2,10000';
        }

        return $rules;
    }
}
