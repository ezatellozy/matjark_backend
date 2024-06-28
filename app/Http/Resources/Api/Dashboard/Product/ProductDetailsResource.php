<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Rate\RateResource;
use App\Http\Resources\Api\Dashboard\Size\SizeAdminResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
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

        $sizes = $this->where('product_id',$this->product_id)->where('color_id',$this->color_id)->get();

        return [
            'id'        => (int) $this->id,
            'color'     => $this->color ? new ColorResource($this->color) : null,
            'sizes'      => SizeAdminResource::collection($sizes),
            'features'  => $this->features ? ProductFeatureResource::collection($this->features) : null,
            // 'quantity'  => (int) $this->quantity,
            // 'price'     => (double) $this->price,
            'code' => (string)$this->code,
            'rate_avg'  => (double) $this->rate_avg,
            'media'     => $this->media ? ProductMediaResource::collection($this->media) : null,
            'have_sale' => (bool) $this->have_sale,
            'rates'     => RateResource::collection($rates),
        ];
    }
}
