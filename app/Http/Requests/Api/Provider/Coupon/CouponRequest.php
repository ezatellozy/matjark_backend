<?php

namespace App\Http\Requests\Api\Provider\Coupon;

use App\Http\Requests\Api\ApiMasterRequest;

class CouponRequest extends ApiMasterRequest
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
        $status = isset($this->coupon) ? 'nullable' : 'required';
        $startAT = 'required|date|after:' . now() ;
if($this->coupon){
        $startAT = 'nullable|date';
    
}
        $rules = [
            'image'             => 'nullable|file',
            'code'              => $status . '|string|unique:coupons,code,' . $this->coupon,
            'start_at'          =>   $startAT,
            'end_at'            => $status . '|date|after:start_at',
            'is_active'         => 'nullable|in:0,1',
            'discount_type'     => $status . '|in:value,percentage',
            'discount_amount'   => $status . '|numeric',
            'max_discount'      => 'nullable|numeric',
            'max_used_num'      => 'nullable|numeric',
            'num_of_used' => 'nullable|numeric',
            'max_used_for_user' => 'nullable|numeric|max:' . $this->max_used_num,
            'applly_coupon_on'  => $status . '|in:all,special_products,except_products,special_categories,except_categories',
            'apply_ids'         => 'required_unless:applly_coupon_on,all|array',
            'addtion_options'   => 'nullable|in:free_shipping',
        ];

        if (in_array($this->applly_coupon_on, ['special_products', 'except_products'])) {
            $rules['apply_ids.*'] = 'exists:product_details,id|distinct';
        }

        if (in_array($this->applly_coupon_on, ['special_categories', 'except_categories'])) {
            $rules['apply_ids.*'] = 'exists:categories,id|distinct'; // exists:categories,id,position,second_sub
        }

        return $rules;
    }
}
