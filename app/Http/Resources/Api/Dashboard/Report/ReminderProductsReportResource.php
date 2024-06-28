<?php

namespace App\Http\Resources\Api\Dashboard\Report;

use App\Http\Resources\Api\App\User\UserItemResource;
use App\Http\Resources\Api\App\User\UserResource;
use App\Http\Resources\Api\Dashboard\FlashSale\FlashSaleProductDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;
use App\Http\Resources\Api\Dashboard\Product\ProductItemResource;
use App\Models\OrderProduct;
use App\Models\ProductDetails;

class ReminderProductsReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        

        return [
            'id'                    => $this->id,
            'user'                  => UserItemResource::make($this->user),
            'product'               => ProductItemResource::make(@$this->flash_sale_product->product),
            'flash_sale_product'    => FlashSaleProductDetailResource::make(@$this->flash_sale_product),
            'start_flash_sale_date' => $this->start_flash_sale_date,
            'status'                => $this->status,

        ];
    }
}
