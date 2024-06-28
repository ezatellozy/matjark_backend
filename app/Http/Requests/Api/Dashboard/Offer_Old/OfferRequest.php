<?php

namespace App\Http\Requests\Api\Dashboard\Offer_Old;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Offer;
use App\Models\ProductDetails;
use Illuminate\Http\Exceptions\HttpResponseException;

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

        $rules = [
            'image'               => $status.'|file',
            'start_at'            => 'required|date|after:'.now(),
            'end_at'              => 'required|date|after:start_at',
            'is_active'           => 'nullable|in:0,1',
            'discount_type'       => $status.'|in:value,percentage',
            'discount_amount'     => $status.'|numeric',
            'max_use'             => $status.'|numeric',
            'num_of_use'          => 'nullable|numeric',
            'ordering'            => 'nullable|numeric|unique:offers,ordering,'. $this->offer,
            'product_detail_id'   => $status.'|array|min:1',
            'product_detail_id.*' => 'exists:product_details,id|distinct'
        ];

        // if ( Offer::whereBetween('start_at', [$this->start_at, $this->end_at])->orWhereBetween('end_at', [$this->start_at, $this->end_at])->orWhere('start_at', '<', $this->start_at)->where('end_at', '>', $this->end_at)->first() )
        // {
        //     throw new HttpResponseException(response()->json([
        //         'status' => 'fail',
        //         'data' => null,
        //         'message' => trans('dashboard.error.there_is_another_offer_at_the_same_time'),
        //     ], 422));
        // }

        $offer_product_detail_ids = ProductDetails::whereHas('offerProductDetails', function ($query) {
            $query->whereBetween('start_at', [$this->start_at, $this->end_at])->orWhereBetween('end_at', [$this->start_at, $this->end_at])->orWhere('start_at', '<', $this->start_at)->where('end_at', '>', $this->end_at);
        })->pluck('id')->toArray();

        if (isset($this->product_detail_id))
        {
            foreach ($this->product_detail_id as $product_detail_id)
            {
                $product_detail = ProductDetails::find($product_detail_id);

                if ($product_detail and $this->discount_type == 'value' and $product_detail->price <= $this->discount_amount)
                {
                    throw new HttpResponseException(response()->json([
                        'status'  => 'fail',
                        'data'    => null,
                        'message' => trans('dashboard.error.discount_value_more_than_product_price'),
                    ], 422));
                }

                if (in_array($product_detail_id, $offer_product_detail_ids))
                {
                    throw new HttpResponseException(response()->json([
                        'status'  => 'fail',
                        'data'    => null,
                        'message' => trans('dashboard.error.this_product_in_another_offer'),
                    ], 422));
                }

                // if ($product_detail and $product_detail->inActiveFlashSale)
                // {
                //     throw new HttpResponseException(response()->json([
                //         'status' => 'fail',
                //         'data' => null,
                //         'message' => trans('dashboard.error.cant_add_this_product_because_it_in_flash_sale'),
                //     ], 422));
                // }
            }
        }

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name'] = $status.'|string|between:2,45';
            $rules[$locale.'.desc'] = 'nullable|string|between:2,45';
        }

        return $rules;
    }
}
