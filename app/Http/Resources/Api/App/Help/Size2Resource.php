<?php

namespace App\Http\Resources\Api\App\Help;

use App\Http\Resources\Api\Website\Product\ProductFeaturesResource;
use App\Models\FavouriteProduct;
use App\Models\OrderRate;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\Rate\RateResource;
use App\Http\Resources\Api\Website\CommonQuestion\CommonQuestionResource;


class Size2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if(auth()->guard('api')->user() != null) {

            if(FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null, 'product_detail_id' => $this->id])->first()  != null) {
                $fav = true;
            } else {
                $fav = false;
            }

        } else {

            if($request->guest_token != null) {
                if(FavouriteProduct::where(['guest_token' => $request->guest_token, 'product_detail_id' => $this->id, 'user_id' => null])->first()  != null) {
                    $fav = true;
                } else {
                    $fav = false;
                }
            } else {
                $fav = false;
            }
        }

        $rates =  OrderRate::where(['product_detail_id'=> $this->id ,'status' =>'accepted'])->count();

        return [
            'id'        => $this->size_id,
            'name'      => (string) @$this->size->name,
            'tag'       => (string) @$this->size->tag,
            'price'      => $this->price,
            'currency' => 'SAR',

            'quantity'   => $this->quantity,

            'product_details_id'        => $this->id,



            'rate_avg' => (float)$this->rate_avg,
            'num_of_reviews' =>  (int)OrderRate::where('product_detail_id', $this->id)->where('status','!=','pending')->count(),
            'have_sale' =>  $this->have_sale,
            'code' => (string)$this->code,
            'is_fav' =>   $fav,
            'features' => $this->features ?  ProductFeaturesResource::collection($this->features):null,
            'reviews' =>   RateResource::collection( OrderRate::where(['status' =>'accepted' ,'product_detail_id'=> $this->id])->take(4)->get()),
            'rating' => [
                'total_rating' => (int) OrderRate::where(['status' =>'accepted' ,'product_detail_id'=> $this->id])->count(),
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
