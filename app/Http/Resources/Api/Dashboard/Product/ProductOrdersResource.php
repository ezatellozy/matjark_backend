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

class ProductOrdersResource extends JsonResource
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
            'order_id' => @$this->order->id,
            'client' => $this->order && $this->order->client ? SelectClientResource::make($this->order->client) : null,
            'color' => ColorItemResource::make($this->color),
            'size' => SizeItemResource::make($this->size),
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,

        ];
    }

}
