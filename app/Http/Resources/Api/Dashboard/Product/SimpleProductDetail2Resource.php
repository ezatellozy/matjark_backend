<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Coupon\CouponProductDetailResource;
use App\Http\Resources\Api\Dashboard\Offer\OfferProductDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductDetail2Resource extends JsonResource
{

    protected static $using = [];

    public static function using($using = [])
    {
        static::$using = $using;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // $buy_apply = $this->merge(static::$using)->data['buy_apply'];

        // return [
        //     'product' => [
        //         'id'    => $buy_apply->count() > 0 ? ($buy_apply[0])->product->id : null, 
        //         'name'  => $buy_apply->count() > 0 ? ($buy_apply[0])->product->name : null, 
        //         'code'  => $buy_apply->count() > 0 ? ($buy_apply[0])->product->code : null, 
        //     ],
        //     'product_details_show' => OfferProductDetailResource::collection($buy_apply),
        //     'product_details_ids'   => $buy_apply->pluck('id')->toArray(),
        // ];

        return [
            'product'              => ['id' => $this->id, 'name' => $this->name ,'code'=> $this->code],
            'product_details_show' => OfferProductDetailResource::collection($this->productDetails),
            'product_details_ids'   => $this->product_detail_ids,
        ];
    }
}
