<?php

namespace App\Http\Resources\Api\Provider\Product;

use App\Http\Resources\Api\Provider\Color\ColorResource;
use App\Http\Resources\Api\Provider\Rate\RateResource;
use App\Http\Resources\Api\Provider\Size\SizeResource;
use App\Models\OrderRate;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $rates = OrderRate::where('product_detail_id', $this->id)->get();
        return [
            'id'        => (int) $this->id,
            'color'     => $this->color ? new ColorResource($this->color) : null,
            'size'      => $this->size ? new SizeResource($this->size) : null,
            'features'  => $this->features ? ProductFeatureResource::collection($this->features) : null,
            'quantity'  => (int) $this->quantity,
            'price'     => (double) $this->price,
            'code' => (string)$this->code,
            'rate_avg'  => (double) $this->rate_avg,
            'media'     => $this->media ? ProductMediaResource::collection($this->media) : null,
            'have_sale' => (bool) $this->have_sale,
            'rates'     => RateResource::collection($rates),
        ];
    }
}
