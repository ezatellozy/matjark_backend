<?php

namespace App\Http\Resources\Api\Dashboard\FlashSale;

use App\Models\ProductDetails;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashSaleProductV2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $ProductDetails = ProductDetails::where('id',$this->productDetail != null ? $this->productDetail->id : 0)->get();

        return [
            'id'                   => (int) $this->id,
            'quantity'             => (int) $this->quantity,
            'quantity_for_user'    => (int) $this->quantity_for_user,
            'ordering'             => (int) $this->ordering,
            'discount_type'        => (string) $this->discount_type,
            'discount_type_details'=> ['id' => $this->discount_type , 'name' => $this->discount_type],
            'discount_amount'      => (double) $this->discount_amount,
            'price_before'         => (double) $this->price_before,
            'price_after'          => (double) $this->price_after,
            'product'              => $this->product ? ['id' => $this->product->id, 'name' => $this->product->name] : null,
            'product_details_show' => $this->product ? FlashSaleProductDetailResource::collection($this->product->productDetails) : [],
            // 'product_details_selected' => $this->product ? FlashSaleProductDetailResource::collection($ProductDetails) : [],
            // 'product_details_id'   => $this->product_detail_id,
            'product_details_id'   => $this->productDetail != null ? $this->productDetail->id : 0
        ];
    }
}
