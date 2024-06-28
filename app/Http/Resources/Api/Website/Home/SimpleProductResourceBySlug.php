<?php

namespace App\Http\Resources\Api\Website\Home;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\CommonQuestion\CommonQuestionResource;
use App\Models\Color;

use App\Http\Resources\Api\App\Help\{ColorResource, Size2Resource, SizeResource};
use App\Http\Resources\Api\Website\Product\ProductDetailsResource;
use App\Http\Resources\Api\Website\Meta\MetaResource;
use App\Http\Resources\Api\Website\Product\ProductDetails2Resource;
use App\Models\{FavouriteProduct, OrderRate};

class SimpleProductResourceBySlug extends JsonResource
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

        // $colorId = $request->color_id != null ? $request->color_id : ($colorIds->first() ?   $colorIds->first()->id : null);

        if($request->color_id != null) {
            $colorId = $request->color_id;
        } else {
            if($colorIds->first()) {
                $colorId = $colorIds->first()->id;
            } else {
                $colorId = null;
            }
        }

        $selectedColor = Color::find($colorId);

        // $sizeIds = Size::whereIn('id', $this->productDetails()->groupBy('size_id')->pluck('size_id'))->get();
        // $sizeId = $request->size_id != null ? $request->size_id : ($sizeIds->first() ? $sizeIds->first()->id : null);

        // $productDetails = $this->productDetails()->when($colorId != null, function ($q) use ($colorId) {
        //     $q->where('color_id', $colorId);
        // })->groupBy('color_id')->get();

        $productDetails = $this->productDetails()->when($colorId != null, function ($q) use ($colorId) {
            $q->where('color_id', $colorId);
        })->get();

        ///////////////////////////////////////////////////////////////////////

        // $sizes = $this->productDetails()->where('product_id',$this->id)->where('color_id',$colorId)->get();

        $images = $productDetails->first() ? $productDetails->first()->images : [];

        //////////////////////////////////////////////////////////////////////////

        // $fav = auth()->guard('api')->user() != null ? (FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null, 'product_detail_id' => $this->id])->first()  != null ? true : false) : ($request->guest_token != null ? (FavouriteProduct::where(['guest_token' => $request->guest_token, 'product_detail_id' => $this->id, 'user_id' => null])->first()  != null ? true : false) : false);
        // $rates =  OrderRate::where(['product_detail_id'=> $this->id ,'status' =>'accepted'])->count();

        $product_detail_id = $productDetails->first() ? $productDetails->first()->id : null;

        if(auth()->guard('api')->user() != null) {

            if(FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null, 'product_detail_id' => $product_detail_id])->first()  != null) {
                $fav = true;
            } else {
                $fav = false;
            }

        } else {

            if($request->guest_token != null) {
                if(FavouriteProduct::where(['guest_token' => $request->guest_token, 'product_detail_id' => $product_detail_id, 'user_id' => null])->first()  != null) {
                    $fav = true;
                } else {
                    $fav = false;
                }
            } else {
                $fav = false;
            }
        }

        return [
            'id' =>(int)$this->id,
            'name' =>(string)$this->name,
            'desc' => (string)$this->desc,
            'slug' => (string)$this->slug,
            'quantity'  => $this->productDetails()->sum('quantity'),

            'is_fav' => $fav,
            'currency' => 'SAR',

            // 'product_details' => SimpleProductDetailResource::collection($this->productDetails()->groupBy('color_id')->get()),

            // 'product_details' => SimpleProductDetailResource::collection($this->productDetails),
            // 'product_details' => SimpleProductDetailResource::collection($this->productDetails()->groupBy('color_id')->get()),

            // 'common_questions' => CommonQuestionResource::collection($this->commonQuestions)

            // 'is_fav' =>false,

            'selected_color' =>   ColorResource::make($selectedColor),

            'product_colors' =>   ColorResource::collection($colorIds),

            //'product_details' => ProductDetails2Resource::collection($productDetails),

            'product_details' => [
                'id'     => $product_detail_id,
                'sizes'  => Size2Resource::collection($productDetails),
                'images' => $images,   
            ],

            // 'size' =>   SizeResource::collection($sizeIds),
            'size_guide' => $this->size_guide,

            'return_policy' =>  app()->getLocale() == 'ar' ? (string) setting('return_policy_ar')  : (string) setting('return_policy_en') ,

            // 'common_questions' => CommonQuestionResource::collection($this->commonQuestions)
            'meta_data' => MetaResource::make($this->metas),
            // 'meta_keywords' => $this->keywords,

        ];
    }
}
