<?php

namespace App\Http\Requests\Api\Dashboard\CommonQuestion;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\CommonQuestion;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommonQuestionRequest extends ApiMasterRequest
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
        $status = isset($this->commonQuestion) ? 'nullable' : 'required';

        $rules = [
            'product_id' => 'required|exists:products,id'
            ];
        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.question'] = $status.'|string';
            $rules[$locale.'.answer'] = $status.'|string';
        }

        return $rules;
    }
}