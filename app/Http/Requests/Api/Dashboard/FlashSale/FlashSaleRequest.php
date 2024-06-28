<?php

namespace App\Http\Requests\Api\Dashboard\FlashSale;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\FlashSale;
use App\Models\ProductDetails;
use Illuminate\Http\Exceptions\HttpResponseException;

class FlashSaleRequest extends ApiMasterRequest
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
        $status = isset($this->flash_sale) ? 'nullable' : 'required';

        $rules = [
            'start_at'                                => $status.'|date|after:'.now(),
            'end_at'                                  => 'required|date|after:start_at|after:'.now(),
            'is_active'                               => 'nullable|in:0,1',
            'flash_sale_products'                     => $status.'|array|min:1',
            'flash_sale_products.*.id'                => 'nullable|exists:flash_sale_products,id|distinct',
            'flash_sale_products.*.product_detail_id' => 'required|exists:product_details,id|distinct',
            'flash_sale_products.*.quantity'          => 'required|numeric',
            'flash_sale_products.*.quantity_for_user' => 'required|numeric',
            'flash_sale_products.*.ordering'          => 'nullable|numeric',
            'flash_sale_products.*.discount_type'     => 'required|in:value,percentage',
            'flash_sale_products.*.discount_amount'   => 'required|numeric',
            // 'flash_sale_products.*.price_before'      => 'nullable|numeric',
            // 'flash_sale_products.*.price_after'       => 'required|numeric',
        ];
        // if ( FlashSale::where('id', '!=', $this->flash_sale)->whereBetween('start_at', [$this->start_at, $this->end_at])->orWhereBetween('end_at', [$this->start_at, $this->end_at])->orWhere('start_at', '<', $this->start_at)->where('end_at', '>', $this->end_at)->first() )
        // {
        //     throw new HttpResponseException(response()->json([
        //         'status' => 'fail',
        //         'data' => null,
        //         'message' => trans('dashboard.error.there_is_another_flash_sale_at_the_same_time'),
        //     ], 422));
        // }

        $lang = app()->getLocale();

        if (isset($this->flash_sale_products))
        {
            foreach ($this->flash_sale_products as $flash_sale_product)
            {
                $product_details = ProductDetails::find($flash_sale_product['product_detail_id']);

                $check = $this->checkPrice($flash_sale_product['discount_type'], $flash_sale_product['discount_amount'], $flash_sale_product['price_before'] ?? $product_details->price, $flash_sale_product['price_after']);

                if (! $check)
                {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        //'message' => trans('dashboard.error.price_after_not_true'),
                        'message' => $lang == 'en' ? 'pice after discount not true' : 'السعر بعد الخصم غير صحيح'
                    ], 422));
                }

                if ($product_details and ! $flash_sale_product['price_before'] and $flash_sale_product['discount_type'] == 'value' and $product_details->price <= $flash_sale_product['discount_amount'])
                {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        //'message' => trans('dashboard.error.discount_amount_more_than_price'),
                        'message' => $lang == 'en' ? 'discount amount more than price' : 'قيمة الخصم أكبر من السعر'
                    ], 422));
                }

                if ($product_details and (
                    $flash_sale_product['quantity'] > $product_details->quantity or
                    $product_details->getflashSaleQuantity($this->flash_sale) + $flash_sale_product['quantity'] > $product_details->quantity
                    )
                )
                {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        //'message' => trans('dashboard.error.quantity_not_avaliable'),
                        'message' => $lang == 'en' ? 'quantity not avaliable' : 'الكمية غير متاحة'
                    ], 422));
                }

                if ($product_details and $flash_sale_product['quantity_for_user'] > $flash_sale_product['quantity'])
                {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        //'message' => trans('dashboard.error.quantity_for_user_more_quantity'),
                        'message' => $lang == 'en' ? 'quantity for user more quantity' : 'الكمية للمستخدم الواحد اكبر من الكمية الكلية'
                    ], 422));
                }

                if ($product_details && $product_details->inActiveOffer())
                {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        //'message' => trans('dashboard.error.cant_add_this_product_because_it_in_offer'),
                        'message' => $lang == 'en' ? 'cant add this product because it in offer' : 'لا يمكن أضافة هذا المنتج لانه عرض'
                    ], 422));
                }
            }
        }

        return $rules;
    }


    protected function getValidatorInstance()
    {
        $data = $this->all();

        $lang = app()->getLocale();

        if (isset($data['flash_sale_products']))
        {
            for ($i = 0; $i < count($data['flash_sale_products']); $i++)
            {
                if(array_key_exists('product_detail_id',$data['flash_sale_products'][$i])) {
                    $product_detail = ProductDetails::find($data['flash_sale_products'][$i]['product_detail_id']);
                } else {
                    $product_detail = null;
                }

                if($product_detail == null) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        //'message' => trans('dashboard.error.price_after_not_true'),
                        'message' => $lang == 'en' ? 'please choose one flash sale product a least' : 'من فضلك اختر منتج واحد علي الاقل'
                    ], 422));    
                }
                
                $data['flash_sale_products'][$i]['product_id'] = $product_detail ? $product_detail->product_id : null;

                if (! isset($data['flash_sale_products'][$i]['price_before']))
                {

                    $data['flash_sale_products'][$i]['price_before'] = $product_detail->price;

                    if($data['flash_sale_products'][$i]['discount_type'] == 'value') {
                        $data['flash_sale_products'][$i]['price_after'] = $product_detail->price - $data['flash_sale_products'][$i]['discount_amount'];
                    } else {
                        $data['flash_sale_products'][$i]['price_after'] = $product_detail->price - round(($data['flash_sale_products'][$i]['discount_amount'] * $product_detail->price) / 100);
                    }

                }

                $data['flash_sale_products'][$i]['flash_sale_id'] = $this->flash_sale;

            }
        }

        // if(! isset($data['start_at']) && isset($this->flash_sale)) {

        //     $flash_sale = FlashSale::find($this->flash_sale);

        //     if($flash_sale) {
        //         $data['start_at'] = $flash_sale->start_at;
        //     }

        // }

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }

    public function checkPrice($discount_type, $discount_amount, $price_before, $price_after)
    {
        $actually_price = 0;

        if ($discount_type == 'percentage')
        {
            $discount_amount = $discount_amount / 100;
            $actually_price = $price_before - $price_before * $discount_amount;
        }
        else
        {
            $actually_price = $price_before - $discount_amount;
        }

        return $actually_price == $price_after;
    }


    public function messages()
    {

        $arr = [];

        $lang = app()->getLocale();

        if($lang == 'en') {

            if(! empty(request()->flash_sale_products)) {
                foreach(request()->flash_sale_products as $key => $key2) {
                    $arr['flash_sale_products.'.$key.'.id.exists'] = ' flash sale products id '  .($key + 1). ' is not found';
                    $arr['flash_sale_products.'.$key.'.id.distinct'] = ' flash sale products id '  .($key + 1). ' must be distinct';
                   
                    $arr['flash_sale_products.'.$key.'.product_detail_id.exists'] = ' flash sale product detail id '  .($key + 1). ' is not found';
                    $arr['flash_sale_products.'.$key.'.product_detail_id.distinct'] = ' flash sale product detail id '  .($key + 1). ' must be distinct';

                    $arr['flash_sale_products.'.$key.'.quantity.required'] = ' flash sale product quantity '  .($key + 1). ' is required';
                    $arr['flash_sale_products.'.$key.'.quantity.numeric'] = ' flash sale product quantity '  .($key + 1). ' must be numeric';
                    $arr['flash_sale_products.'.$key.'.quantity_for_user.required'] = ' flash sale product quantity for user '  .($key + 1). ' is required';
                    $arr['flash_sale_products.'.$key.'.quantity_for_user.numeric'] = ' flash sale product quantity for user '  .($key + 1). ' must be numeric';
                    $arr['flash_sale_products.'.$key.'.ordering.required'] = ' flash sale product ordering '  .($key + 1). ' is required';
                    $arr['flash_sale_products.'.$key.'.ordering.numeric'] = ' flash sale product ordering '  .($key + 1). ' must be numeric';
                    $arr['flash_sale_products.'.$key.'.discount_type.required'] = ' flash sale product discount type '  .($key + 1). ' is required';
                    $arr['flash_sale_products.'.$key.'.discount_type.numeric'] = ' flash sale product discount type '  .($key + 1). ' must be numeric';
                    $arr['flash_sale_products.'.$key.'.discount_amount.required'] = ' flash sale product discount amount. '  .($key + 1). ' is required';
                    $arr['flash_sale_products.'.$key.'.discount_amount.numeric'] = ' flash sale product discount amount '  .($key + 1). ' must be numeric';
                    $arr['flash_sale_products.'.$key.'.price_before.required'] = ' flash sale product price before. '  .($key + 1). ' is required';
                    $arr['flash_sale_products.'.$key.'.price_before.numeric'] = ' flash sale product price before '  .($key + 1). ' must be numeric';
                    $arr['flash_sale_products.'.$key.'.price_after.required'] = ' flash sale product price after. '  .($key + 1). ' is required';
                    $arr['flash_sale_products.'.$key.'.price_after.numeric'] = ' flash sale product price after '  .($key + 1). ' must be numeric';
                }
            }


        } else {

            if(! empty(request()->flash_sale_products)) {
                foreach(request()->flash_sale_products as $key => $key2) {

                    $arr['flash_sale_products.'.$key.'.id.exists'] = ' رقم خصومات المنتجات '  .($key + 1). ' غير موجود ';
                    $arr['flash_sale_products.'.$key.'.id.distinct'] = ' رقم خصومات المنتجات '  .($key + 1). ' لا يجب ان يحتوي علي قيم متكررة ';
                    
                    $arr['flash_sale_products.'.$key.'.product_detail_id.exists'] = ' رقم  تفاصيل خصومات المنتج '  .($key + 1). ' غير موجود ';
                    $arr['flash_sale_products.'.$key.'.product_detail_id.distinct'] = ' رقم  تفاصيل خصومات المنتج '  .($key + 1). ' لا يجب ان يحتوي علي قيم متكررة ';

                    $arr['flash_sale_products.'.$key.'.quantity.required'] = ' الكمية للعميل  تفاصيل خصومات المنتج '  .($key + 1). ' مطلوب';
                    $arr['flash_sale_products.'.$key.'.quantity.numeric'] = ' الكمية للعميل  تفاصيل خصومات المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';

                    $arr['flash_sale_products.'.$key.'.quantity_for_user.required'] = ' رقم  تفاصيل خصومات المنتج '  .($key + 1). ' مطلوب';
                    $arr['flash_sale_products.'.$key.'.quantity_for_user.numeric'] = ' رقم  تفاصيل خصومات المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';

                    $arr['flash_sale_products.'.$key.'.ordering.required'] = ' ترتيب  تفاصيل خصومات المنتج '  .($key + 1). ' مطلوب';
                    $arr['flash_sale_products.'.$key.'.ordering.numeric'] = ' ترتيب  تفاصيل خصومات المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';

                    $arr['flash_sale_products.'.$key.'.discount_type.required'] = ' نوع الخصم  تفاصيل خصومات المنتج '  .($key + 1). ' مطلوب';
                    $arr['flash_sale_products.'.$key.'.discount_type.numeric'] = ' نوع الخصم  تفاصيل خصومات المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';

                    $arr['flash_sale_products.'.$key.'.discount_amount.required'] = ' قيمة الخصم  تفاصيل خصومات المنتج '  .($key + 1). ' مطلوب';
                    $arr['flash_sale_products.'.$key.'.discount_amount.numeric'] = ' قيمة الخصم  تفاصيل خصومات المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';
                    
                    $arr['flash_sale_products.'.$key.'.price_before.required'] = ' سعر قبل الخصم  تفاصيل خصومات المنتج '  .($key + 1). ' مطلوب';
                    $arr['flash_sale_products.'.$key.'.price_before.numeric'] = ' سعر قبل الخصم  تفاصيل خصومات المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';
                    
                    $arr['flash_sale_products.'.$key.'.price_after.required'] = ' سعر بعد الخصم  تفاصيل خصومات المنتج '  .($key + 1). ' مطلوب';
                    $arr['flash_sale_products.'.$key.'.price_after.numeric'] = ' سعر بعد الخصم  تفاصيل خصومات المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';

                }
            }

        }


        return $arr;
    }

}
