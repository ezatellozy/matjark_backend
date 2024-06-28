<?php

namespace App\Http\Resources\Api\Dashboard\ReturnOrder;

use App\Http\Resources\Api\Dashboard\Product\SimpleProductDetailResource;
use App\Models\OrderProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnOrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $order_product = OrderProduct::find($this->order_product_id);

        return [
            "id"                => (int) $this->id,
            "product"           => $this->productDetail ? new SimpleProductDetailResource($this->productDetail) : null,
            "return_qty"        => (int) $this->quantity,
            "order_product_qty" => $order_product ? (int) $order_product->quantity : null,
            "price"             => (double) $this->price,
            "status"            => (string) $this->status,
            "reject_reason"     => (string) $this->reject_reason,
            'status_trans'      => (string) trans('app.return_orders.' . $this->status),
        ];
    }
}
