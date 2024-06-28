<?php

namespace App\Http\Requests\Api\App\Favourite;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\ProductDetails;
use Illuminate\Foundation\Http\FormRequest;

class FavRequest extends ApiMasterRequest
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
        return [
            'product_detail_id' => 'required|exists:product_details,id',
            'product_id' => 'nullable',
            'guest_token' => 'nullable|string',
        ];
    }

    public function getValidatorInstance()
    {
        $data = $this->all();
       
        $product_detail_id = $this->route('product_detail_id');

        $productDetails = ProductDetails::find($product_detail_id);

        $data['product_detail_id'] = $product_detail_id;
        $data['product_id'] = $productDetails != null ? $productDetails->product_id : null;

        $this->getInputSource()->replace($data);
        
        return parent::getValidatorInstance();
    }
}
