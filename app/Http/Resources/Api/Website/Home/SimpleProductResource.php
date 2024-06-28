<?php

namespace App\Http\Resources\Api\Website\Home;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\CommonQuestion\CommonQuestionResource;
use App\Models\Color;

use App\Http\Resources\Api\App\Help\{ColorResource, Size2Resource, Size3Resource, SizeResource};
use App\Http\Resources\Api\Website\Product\ProductDetailsResource;
use App\Http\Resources\Api\Website\Meta\MetaResource;
use App\Http\Resources\Api\Website\Product\ProductDetails2Resource;
use App\Models\{FavouriteProduct, OrderRate};

class SimpleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $colorsArr = $this->productDetails()->groupBy('color_id')->pluck('color_id')->toArray();

        $colorIds = Color::whereIn('id', $colorsArr)->get();

        if ($request->color_id != null) {
            $colorId = $request->color_id;
        } else {
            if ($colorIds->first()) {
                $colorId = $colorIds->first()->id;
            } else {
                $colorId = null;
            }
        }

        if (is_array($colorId)) {
            $colorId = $colorId[0];
            $selectedColor = Color::find($colorId[0]);
        } else {
            $selectedColor = Color::find($colorId);
        }

        $selectedColor = Color::find($colorId);

        $productDetails = $this->productDetails()->when($colorId != null, function ($q) use ($colorId) {
            $q->where('color_id', $colorId);
        })->get();

        ///////////////////////////////////////////////////////////////////////

        $images = $productDetails->first() ? $productDetails->first()->images : [];

        $product_detail_id = $productDetails->first() ? $productDetails->first()->id : null;

        if (auth()->guard('api')->user() != null) {

            if (FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null, 'product_detail_id' => $product_detail_id])->first()  != null) {
                $fav = true;
            } else {
                $fav = false;
            }
        } else {

            if ($request->guest_token != null) {
                if (FavouriteProduct::where(['guest_token' => $request->guest_token, 'product_detail_id' => $product_detail_id, 'user_id' => null])->first()  != null) {
                    $fav = true;
                } else {
                    $fav = false;
                }
            } else {
                $fav = false;
            }
        }

        return [
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'desc' => (string)$this->desc,
            'slug' => (string)$this->slug,
            'quantity'  => $this->productDetails()->sum('quantity'),

            'is_fav' => $fav,

            'currency' => 'SAR',

            'selected_color' =>   ColorResource::make($selectedColor),

            'product_colors' =>   ColorResource::collection($colorIds),

            //'product_details' => ProductDetails2Resource::collection($productDetails),

            'rate_avg' => $productDetails->first() != null ? $productDetails->first()->rate_avg : 0,


            'product_details' => [
                'id'     => $product_detail_id,
                'sizes'  => Size3Resource::make($productDetails->first()),
                'images' => $images,
            ],



        ];
    }
}
