<?php

namespace App\Http\Resources\Api\Website\Product;

use App\Http\Resources\Api\App\Help\{ColorResource, SizeResource};
use App\Models\{Color, Size};
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\CommonQuestion\CommonQuestionResource;
use App\Http\Resources\Api\Website\Meta\MetaResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $colorIds = Color::whereIn('id', $this->productDetails()->groupBy('color_id')->pluck('color_id'))->get();
        $colorId = $request->color_id != null ? $request->color_id : ($colorIds->first() ?   $colorIds->first()->id : null);
        // $sizeIds = Size::whereIn('id', $this->productDetails()->groupBy('size_id')->pluck('size_id'))->get();
        // $sizeId = $request->size_id != null ? $request->size_id : ($sizeIds->first() ? $sizeIds->first()->id : null);
        return [
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'slug' => (string)$this->slug,
            'desc' => (string)$this->desc,
                           
            'quantity'  => $this->productDetails()->sum('quantity'),

            'color' =>   ColorResource::collection($colorIds),
            // 'size' =>   SizeResource::collection($sizeIds),
            'size_guide' => $this->size_guide,
            'return_policy' =>  app()->getLocale() == 'ar' ? (string) setting('return_policy_ar')  : (string) setting('return_policy_en') ,
            'product_details' => ProductDetailsResource::collection($this->productDetails()->when($colorId != null, function ($q) use ($colorId) {
                $q->where('color_id', $colorId);
            })->get()),
            // 'common_questions' => CommonQuestionResource::collection($this->commonQuestions)
            'meta_data' => MetaResource::make($this->metas)
        ];


    }
}
