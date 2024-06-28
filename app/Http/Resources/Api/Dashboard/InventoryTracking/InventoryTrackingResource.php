<?php

namespace App\Http\Resources\Api\Dashboard\InventoryTracking;

use App\Http\Resources\Api\Dashboard\Color\ColorResource;
use App\Http\Resources\Api\Dashboard\Product\ProductFeatureResource;
use App\Http\Resources\Api\Dashboard\Product\ProductMediaResource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductResource;
use App\Http\Resources\Api\Dashboard\Size\SizeResource;
use App\Models\Feature;
use App\Models\FeatureValue;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Feature\FeatureValueResource;


class InventoryTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // dd($this->features);

        $featuresArr = null;

        if($this->features && ! empty($this->features) && gettype($this->features) == 'array') {

            foreach($this->features as $arr) {
                $feature = Feature::find($arr['feature_id']);
                $value   = FeatureValue::find($arr['value_id']);
                $values  = FeatureValue::where('feature_id', $arr['feature_id'])->get();
            }

            $featuresArr = [
                'feature' => $feature ? ['id' => $feature->id, 'name' => $feature->name,'values'  => FeatureValueResource::collection($values)] : null ,
                'value'   => $value ? ['id'   => $value->id, 'value' => $value->value] : null,
                'values'  => FeatureValueResource::collection($values),
            ];

        }

        return [
            'id'        => (int) $this->id,
            'product'   => $this->product ? new SimpleProductResource($this->product) : null,
            'color'     => $this->color ? new ColorResource($this->color) : null,
            'size'      => $this->size ? new SizeResource($this->size) : null,
            // 'features'  => $this->features ? ProductFeatureResource::collection($this->features) : null,
            'features'  => $featuresArr,
            'quantity'  => (int) $this->quantity,
            'price'     => (double) $this->price,
            'rate_avg'  => (double) $this->rate_avg,
            'media'     => $this->media ? ProductMediaResource::collection($this->media) : null,
            'have_sale' => (bool) $this->have_sale,
        ];
    }
}
