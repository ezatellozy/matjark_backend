<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Rate\RateResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
use App\Models\OrderRate;
use App\Models\ProductDetails;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class ProductDetails2Resource extends JsonResource
{

    // protected static $using = [];

    // public static function using($using = [])
    // {
    //     static::$using = $using;
    // }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // $media = $this->merge(static::$using)->data['media'];

        $rates = OrderRate::where('product_detail_id', $this->id)->get();

        $first_product_details_color = ProductDetails::where('product_id',$this->product_id)->where('color_id',$this->color_id)->first();

        return [
            'id'        => (int) $this->id,
            'color'     => $this->color ? new ColorResource($this->color) : null,
            'size'      => $this->size ? new SizeResource($this->size) : null,
            'features'  => $this->features ? ProductFeatureResource::collection($this->features) : null,
            'quantity'  => (int) $this->quantity,
            'price'     => (double) $this->price,
            'code' => (string)$this->code,
            'rate_avg'  => (double) $this->rate_avg,
            // 'media'     => $this->media ? ProductMediaResource::collection($this->media) : null,
            'media'     => $first_product_details_color && $first_product_details_color->media ? ProductMediaResource::collection($first_product_details_color->media) : null,
            // 'media'     => $media ? ProductMediaResource::collection($media) : null,
            'have_sale' => (bool) $this->have_sale,
            'rates'     => RateResource::collection($rates),
        ];
    }
}
