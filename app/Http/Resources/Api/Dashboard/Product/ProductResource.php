<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\Dashboard\Category\CategorySimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;
use App\Http\Resources\Api\Dashboard\CommonQuestion\SimpleCommonQuestionResource;
use App\Models\CartProduct;
use App\Models\Color;
use App\Models\FavouriteProduct;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ReturnOrderProduct;

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
        $locales = [];
        foreach (config('translatable.locales') as $locale) {
            $locales[$locale]['name'] = $this->translate($locale)->name;
            // $locales[$locale]['keywords'] = $this->translate($locale)->keywords;
            $locales[$locale]['slug'] = $this->translate($locale)->slug;
            $locales[$locale]['desc'] = $this->translate($locale)->desc;
            $locales[$locale]['meta_canonical_tag'] = $this->translate($locale)->meta_canonical_tag;

            if($this->metas()->count()){
                $locales[$locale]['meta_tag'] = $this->metas?$this->metas->translate($locale)->meta_tag: null;
                $locales[$locale]['meta_title'] = $this->metas?$this->metas->translate($locale)->meta_title: null;
                $locales[$locale]['meta_description'] = $this->metas?$this->metas->translate($locale)->meta_description: null;
            }
        }

        $colorsArr = $this->productDetails()->groupBy('color_id')->pluck('color_id')->toArray();
        $colors = Color::whereIn('id',$colorsArr)->get();

        $nums_clients = 0;

        $usersArr = Order::whereHas('orderProducts',function($orderProducts) {
            $orderProducts->whereIn('product_id',[$this->id]);
        })->pluck('user_id')->toArray();

        $nums_clients = array_unique($usersArr);

        return [
            'id'              => (int) $this->id,
            'name'            => (string) $this->name,
            // 'keywords'            => (string) $this->keywords,
            'slug'            => (string)$this->slug,
            'desc'            => (string) $this->desc,
            'meta_canonical_tag' => (string) $this->meta_canonical_tag,
            'code'            => $this->code,
            'size_guide' => $this->size_guide,
            'rate_avg'        => (double) $this->rate_avg,
            'is_active'       => (bool) $this->is_active,
            'ordering'        => (int) $this->ordering,
            // 'media' => ProductMediaResource::collection($this->media->whereNull('option')),
            'categories'      => CategorySimpleResource::collection($this->categories),
            'main_category'   => count($this->categories) > 0 ? new CategorySimpleResource(root($this->categories->first())) : null,

            // 'product_details' => ProductDetailsResource::collection($this->productDetails()->groupBy('color_id')->get()),

            'product_details' => ProductDetailsColorsResource::collection($this->productDetails()->groupBy('color_id')->get()),

            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'common_questions' => SimpleCommonQuestionResource::collection($this->commonQuestions),
            'meta_canonical_tag' => $this->metas()->count()?$this->metas->meta_canonical_tag:null,
            // 'meta_data' => $this->metas? new MetaResource($this->metas): null,

            // 'statistics' => [
            //     "nums_order"      => OrderProduct::where('product_id', $this->id)->count(),
            //     "nums_cart"       => CartProduct::where('product_id', $this->id)->count(),
            //     "nums_favourites" => FavouriteProduct::where('product_id', $this->id)->count(),
            //     "nums_returns"    => ReturnOrderProduct::whereHas('productDetail',function($productDetail) {
            //         $productDetail->where('product_id',$this->id);
            //     })->count(),
            //     "nums_clients"    => $nums_clients,
            // ],
        ] + $locales;
    }
}
