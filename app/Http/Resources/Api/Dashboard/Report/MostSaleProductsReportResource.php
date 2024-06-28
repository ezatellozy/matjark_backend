<?php

namespace App\Http\Resources\Api\Dashboard\Report;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;
use App\Http\Resources\Api\Dashboard\Product\ProductItemResource;
use App\Models\OrderProduct;
use App\Models\ProductDetails;

class MostSaleProductsReportResource extends JsonResource
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
            'id'                 => $this->id,
            'product'            => ProductItemResource::make($this),
            'total_qty'          => $this->total_quantity,
        ];
    }
}
