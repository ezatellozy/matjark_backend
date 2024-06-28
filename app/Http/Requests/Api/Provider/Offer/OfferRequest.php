<?php

namespace App\Http\Requests\Api\Provider\Offer;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;
use App\Models\DiscountOfOffer;
use App\Models\ProductDetails;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\Api\Provider\Offer\ApplyOnRule;
use Illuminate\Validation\Rule;

class OfferRequest extends ApiMasterRequest
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
        $status = isset($this->offer) ? 'nullable' : 'required';
        $discount = 'nullable|required_if:type,fix_amount,percentage|in:value,percentage';


        $app_images = isset($this->offer) ? 'nullable|file' : 'nullable|required_if:display_platform,app,both|file';
        $web_images = isset($this->offer) ? 'nullable|file' : 'nullable|required_if:display_platform,website,both|file';

        $start = isset($this->offer) ?   'nullable|date' :  'required|date|after:' . now();
        if ($this->type == 'buy_x_get_y') {
            $discount = 'nullable|required_if:type,buy_x_get_y|in:free_product,percentage';
        }

        $rules = [
            // 'image'              => $status . '|file',
            'start_at'           =>  $start,
            'end_at'             => 'required|date|after:start_at',
            'is_active'          => 'nullable|in:0,1',
            'display_platform'   => 'required|in:app,website,both',
            // 'display_platform'   => 'required|in:app,website',

            'type'               => 'required|in:buy_x_get_y,fix_amount,percentage',
            'is_with_coupon'     => 'required|in:0,1',
            'max_use'            => $status . '|numeric',
            'num_of_use'         => 'required|numeric',
            // 'ordering'           => 'nullable|numeric|unique:offers,ordering,' . $this->offer,
            'ordering' =>  [
                'nullable',
                Rule::unique('offers', 'ordering')->ignore($this->offer)
                    ->where('display_platform', $this->display_platform)

            ],
            'buy_quantity'       => 'nullable|required_if:type,buy_x_get_y|numeric',
            'buy_apply_on'       => 'nullable|required_if:type,buy_x_get_y|in:all,special_products,special_categories',
            'buy_apply_ids'      => ['nullable', 'array', 'required_if:buy_apply_on,special_products,special_categories', new ApplyOnRule($this->buy_apply_on)],
            'get_quantity'       => 'nullable|required_if:type,buy_x_get_y|numeric',
            'get_apply_on'       => 'nullable|required_if:type,buy_x_get_y|in:all,special_products,special_categories',
            'get_apply_ids'      => ['nullable', 'array', 'required_if:get_apply_on,special_products,special_categories', new ApplyOnRule($this->get_apply_on)],
            'discount_type'      => $discount,
            'discount_amount'    => 'nullable|required_if:discount_type,percentage,value',

            // 'discount_type'      => $discount,
            // 'discount_amount'    => 'nullable|required_if:type,fix_amount,percentage',
            'max_discount'       => 'nullable|required_if:type,percentage',
            'apply_on'           => 'nullable|required_if:type,fix_amount,percentage|in:all,special_products,special_categories,special_payment',
            'apply_ids'          => ['nullable', 'required_if:apply_on,special_products,special_categories', new ApplyOnRule($this->apply_on)],
            'payment_type'       => 'nullable|required_if:apply_on,special_payment|in:wallet,card,cash',
            // 'min_type'           => 'nullable|required_if:type,fix_amount,percentage|in:quantity_of_products,amount_of_total_price',
            // 'min_value'          => 'required_with:min_type',
            'app_image_ar'  =>   $app_images,
            'app_image_en'  => $app_images,
            'web_image_ar'  => $web_images,
            'web_image_en'  => $web_images,

            'buy_to_get'         => 'nullable|array',
            'discount_of_offers' => 'nullable|array',
        ];
        foreach (config('translatable.locales') as $locale) {
            $rules[$locale . '.name'] = $status . '|string|between:2,45';
            $rules[$locale . '.desc'] = 'nullable|string|between:2,255';
        }




        if (isset($this->type) && $this->type == 'buy_x_get_y') {
            if (isset($this->buy_apply_on) && isset($this->buy_apply_ids)) {

                if (isset($this->buy_apply_on)  && $this->buy_apply_on == 'special_categories') {
                    $productDetails = ProductDetails::whereHas('product', function ($q) {
                        $q->whereHas('categoryProducts', function ($q) {
                            $q->whereIn('category_id', $this->buy_apply_ids);
                        });
                    })->get();
                    foreach ($productDetails as $productDetail) {
                        if ($productDetail and $productDetail->inActiveFlashSale()) {
                            throw new HttpResponseException(response()->json([
                                'status' => 'fail',
                                'data' => null,
                                'message' => trans('dashboard.error.cant_add_this_product_because_it_in_flash_Sale'),
                            ], 422));
                        }
                    }
                } else {
                    foreach ($this->buy_apply_ids as $buy_apply_id) {
                        $product_details = ProductDetails::find($buy_apply_id);
                        if ($product_details and $product_details->inActiveFlashSale()) {
                            throw new HttpResponseException(response()->json([
                                'status' => 'fail',
                                'data' => null,
                                'message' => trans('dashboard.error.cant_add_this_product_because_it_in_flash_Sale'),
                            ], 422));
                        }
                    }
                }
            }
            if (isset($this->get_apply_ids) && isset($this->get_apply_on)) {



                if (isset($this->get_apply_on)  && $this->get_apply_on == 'special_categories') {
                    $productDetails = ProductDetails::whereHas('product', function ($q) {
                        $q->whereHas('categoryProducts', function ($q) {
                            $q->whereIn('category_id', $this->get_apply_ids);
                        });
                    })->get();
                    foreach ($productDetails as $productDetail) {
                        if ($productDetail and $productDetail->inActiveFlashSale()) {
                            throw new HttpResponseException(response()->json([
                                'status' => 'fail',
                                'data' => null,
                                'message' => trans('dashboard.error.cant_add_this_product_because_it_in_flash_Sale'),
                            ], 422));
                        }
                    }
                } else {
                    foreach ($this->get_apply_ids as $get_apply_id) {
                        $product_details = ProductDetails::find($get_apply_id);
                        if ($product_details and $product_details->inActiveFlashSale()) {
                            throw new HttpResponseException(response()->json([
                                'status' => 'fail',
                                'data' => null,
                                'message' => trans('dashboard.error.cant_add_this_product_because_it_in_flash_Sale'),
                            ], 422));
                        }
                    }
                }
            }
        } else {
            if (isset($this->apply_ids)) {
                foreach ($this->apply_ids as $apply_id) {
                    $product_details = ProductDetails::find($apply_id);
                    if ($product_details and $product_details->inActiveFlashSale()) {
                        throw new HttpResponseException(response()->json([
                            'status' => 'fail',
                            'data' => null,
                            'message' => trans('dashboard.error.cant_add_this_product_because_it_in_flash_Sale'),
                        ], 422));
                    }
                    $offerItemDetail = isset($this->offer) ?  DiscountOfOffer::where('offer_id' , '!=',$this->offer)->whereJsonContains('apply_ids' ,$apply_id )->first() : DiscountOfOffer::whereJsonContains('apply_ids' ,$apply_id )->first();
                    if($offerItemDetail != null){
                        throw new HttpResponseException(response()->json([
                            'status' => 'fail',
                            'data' => null,
                            'message' => trans('dashboard.error.cant_add_this_product_because_it_in_offer'),
                        ], 422));
                    }
                }
            }
        }
        return $rules;
    }

    public function getValidatorInstance()
    {
        $data = $this->all();

        $data['buy_to_get'] = [
            'buy_quantity'    =>  isset($data['buy_quantity']) ? $data['buy_quantity'] : null,
            'buy_apply_on'    =>  isset($data['buy_apply_on']) ? $data['buy_apply_on'] : null,
            'buy_apply_ids'   =>  isset($data['buy_apply_ids']) ? $data['buy_apply_ids'] : null,
            'get_quantity'    =>  isset($data['get_quantity']) ? $data['get_quantity'] : null,
            'get_apply_on'    =>  isset($data['get_apply_on']) ? $data['get_apply_on'] : null,
            'get_apply_ids'   =>  isset($data['get_apply_ids']) ? $data['get_apply_ids'] : null,
            'discount_type'   =>  isset($data['discount_type']) ? $data['discount_type'] : null,
            'discount_amount' =>  isset($data['discount_amount']) ? $data['discount_amount'] : null,
        ];

        $data['discount_of_offers'] = [
            'discount_type'   =>  isset($data['discount_type']) ? $data['discount_type'] : null,
            'discount_amount' =>  isset($data['discount_amount']) ? $data['discount_amount'] : null,
            'max_discount'    => isset($data['max_discount']) ? $data['max_discount'] : null,
            'apply_on'        =>  isset($data['apply_on']) ? $data['apply_on'] : null,
            'apply_ids'       => isset($data['apply_ids']) ? $data['apply_ids'] : null,
            'payment_type'    => isset($data['payment_type']) ? $data['payment_type'] : null,
            'min_type'        =>  isset($data['min_type']) ? $data['min_type'] : null,
            'min_value'       => isset($data['min_value']) ? $data['min_value'] : null,
        ];

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
