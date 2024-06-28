<?php

namespace App\Http\Requests\Api\Provider\FlashSale;

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
            'start_at'                                => 'required|date|after:'.now(),
            'end_at'                                  => 'required|date|after:start_at',
            'is_active'                               => 'nullable|in:0,1',
            'flash_sale_products'                     => $status.'|array|min:1',
            'flash_sale_products.*.id'                => 'nullable|exists:flash_sale_products,id|distinct',
            'flash_sale_products.*.product_detail_id' => 'required|exists:product_details,id|distinct',
            'flash_sale_products.*.quantity'          => 'required|numeric',
            'flash_sale_products.*.quantity_for_user' => 'required|numeric',
            'flash_sale_products.*.ordering'          => 'nullable|numeric',
            'flash_sale_products.*.discount_type'     => 'required|in:value,percentage',
            'flash_sale_products.*.discount_amount'   => 'required|numeric',
            'flash_sale_products.*.price_before'      => 'nullable|numeric',
            'flash_sale_products.*.price_after'       => 'required|numeric',
        ];
        if ( FlashSale::where('id', '!=', $this->flash_sale)->whereBetween('start_at', [$this->start_at, $this->end_at])->orWhereBetween('end_at', [$this->start_at, $this->end_at])->orWhere('start_at', '<', $this->start_at)->where('end_at', '>', $this->end_at)->first() )
        {
            throw new HttpResponseException(response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => trans('dashboard.error.there_is_another_flash_sale_at_the_same_time'),
            ], 422));
        }

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
                        'message' => trans('dashboard.error.price_after_not_true'),
                    ], 422));
                }

                if ($product_details and ! $flash_sale_product['price_before'] and $flash_sale_product['discount_type'] == 'value' and $product_details->price <= $flash_sale_product['discount_amount'])
                {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        'message' => trans('dashboard.error.discount_amount_more_than_price'),
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
                        'message' => trans('dashboard.error.quantity_not_avaliable'),
                    ], 422));
                }

                if ($product_details and $flash_sale_product['quantity_for_user'] > $flash_sale_product['quantity'])
                {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        'message' => trans('dashboard.error.quantity_for_user_more_quantity'),
                    ], 422));
                }

                if ($product_details && $product_details->inActiveOffer())
                {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'data' => null,
                        'message' => trans('dashboard.error.cant_add_this_product_because_it_in_offer'),
                    ], 422));
                }
            }
        }

        return $rules;
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();

        if (isset($data['flash_sale_products']))
        {
            for ($i = 0; $i < count($data['flash_sale_products']); $i++)
            {
                $product_detail = ProductDetails::find($data['flash_sale_products'][$i]['product_detail_id']);
                $data['flash_sale_products'][$i]['product_id'] = $product_detail ? $product_detail->product_id : null;

                if (! isset($data['flash_sale_products'][$i]['price_before']))
                {
                    $data['flash_sale_products'][$i]['price_before'] = $product_detail->price;
                }

                $data['flash_sale_products'][$i]['flash_sale_id'] = $this->flash_sale;
            }
        }

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
}
