<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Client\ClientResource;
use App\Http\Resources\Api\Dashboard\Client\SelectClientResource;
use App\Http\Resources\Api\Dashboard\Color\ColorItemResource;
use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Rate\RateResource;
use App\Http\Resources\Api\Dashboard\Size\SizeAdminResource;
use App\Http\Resources\Api\Dashboard\Size\SizeItemResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
use App\Models\OrderRate;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class ReturnedOrderProductsResource extends JsonResource
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
            'id' => $this->id,
            'color' => $this->productDetail && $this->productDetail->color ? ColorItemResource::make($this->color) : null,
            'size' => $this->productDetail && $this->productDetail->size ? SizeItemResource::make($this->size) : null,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'status' => $this->status,
            'reject_reason' => $this->reject_reason,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,

        ];
    }

}
