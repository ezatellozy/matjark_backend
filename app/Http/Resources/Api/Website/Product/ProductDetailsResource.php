<?php

namespace App\Http\Resources\Api\Website\Product;

use App\Http\Resources\Api\App\Help\{ColorResource, SizeResource};
use App\Http\Resources\Api\Website\Rate\RateResource;
use App\Models\{FavouriteProduct, OrderRate};
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\CommonQuestion\CommonQuestionResource;

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
        $fav = auth()->guard('api')->user() != null ? (FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null, 'product_detail_id' => $this->id])->first()  != null ? true : false) : ($request->guest_token != null ? (FavouriteProduct::where(['guest_token' => $request->guest_token, 'product_detail_id' => $this->id, 'user_id' => null])->first()  != null ? true : false) : false);
        $rates =  OrderRate::where(['product_detail_id'=> $this->id ,'status' =>'accepted'])->count();

        return [
            'id' => (int)$this->id,
            // 'color' => new ColorResource($this->color),
            'size' => new SizeResource($this->size),
            'price' => (float)$this->price,
            'name' =>(string)$this->product->name,
            'currency' => 'SAR',
            'rate_avg' => (float)$this->rate_avg,
            'num_of_reviews' =>  (int)OrderRate::where('product_detail_id', $this->id)->where('status','!=','pending')->count(),
            'have_sale' =>  $this->have_sale,
            'images' => $this->images,
            'code' => (string)$this->code,
            'quantity' => (float)$this->quantity, 
            'is_fav' =>   $fav,
            'features' => $this->features ?  ProductFeaturesResource::collection($this->features):null,
            'reviews' =>   RateResource::collection( OrderRate::where(['status' =>'accepted' ,'product_detail_id'=> $this->id])->take(4)->get()),
            'rating' => [
                'total_rating' => (int) OrderRate::where('product_detail_id', $this->id)->count(),
                'stars_5' =>  $rates   > 0  ?  (float) ((OrderRate::where(['status' =>'accepted','product_detail_id' => $this->id, 'rate' => 5])->count() /  $rates) * 100) : 0,
                'stars_4' => $rates   > 0  ? (float)((OrderRate::where(['status' =>'accepted' ,'product_detail_id'=> $this->id])->where('rate', '<', 5)->where('rate', '>=', 4)->count() / $rates) * 100) : 0,
                'stars_3' => $rates > 0 ? (float)((OrderRate::where(['status' =>'accepted' ,'product_detail_id'=> $this->id])->where('rate', '<', 4)->where('rate', '>=', 3)->count() / $rates) * 100) : 0,
                'stars_2' => $rates > 0  ? (float)((OrderRate::where(['status' =>'accepted' ,'product_detail_id'=> $this->id])->where('rate', '<', 3)->where('rate', '>=', 2)->count() / $rates) * 100) : 0,
                'stars_1' => $rates > 0  ? (float)((OrderRate::where(['status' =>'accepted' ,'product_detail_id'=> $this->id])->where('rate', '<', 2)->where('rate', '>=', 1)->count() / $rates) * 100) : 0,
            ],
            'common_questions' => CommonQuestionResource::collection($this->product->commonQuestions)
                  
        ];
    }
}
