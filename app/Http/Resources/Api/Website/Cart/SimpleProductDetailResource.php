<?php

namespace App\Http\Resources\Api\Website\Cart;

use App\Http\Resources\Api\App\Help\ColorResource;
use App\Http\Resources\Api\App\Help\SizeResource;
use App\Models\FavouriteProduct;
use App\Models\FlashSaleProduct;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductDetailResource extends JsonResource
{

    protected static $using = [];

    public static function using($using = [])
    {
        static::$using = $using;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $fav = auth()->guard('api')->user() != null ? (FavouriteProduct::where(['user_id' => auth('api')->id(), 'guest_token' => null, 'product_detail_id' => $this->id])->first()  != null ? true : false) : ($request->guest_token != null ? (FavouriteProduct::where(['guest_token' => $request->guest_token, 'product_detail_id' => $this->id, 'user_id' => null])->first()  != null ? true : false) : false);


        // $offer_id = $this->merge(static::$using)->data != null && array_key_exists('offer_id',$this->merge(static::$using)->data) ? $this->merge(static::$using)->data['offer_id'] : null;
        // $flash_sale_product_id = $this->merge(static::$using)->data != null && array_key_exists('flash_sale_product_id',$this->merge(static::$using)->data) ? $this->merge(static::$using)->data['flash_sale_product_id'] : null;

        // if($offer_id != null || $flash_sale_product_id != null) {

        //     // $have_sale = $this->have_sale;

        //     if($offer_id != null) {

        //         $offer = Offer::find($offer_id);

        //         if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && $offer != null && ($offer->max_use <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->count())) {
        //             $have_sale = null;
        //         } else {
        //             $have_sale = $this->have_sale;
        //         }

        //     } else {

        //         $flashSalesProduct = FlashSaleProduct::find($flash_sale_product_id);

        //         if (auth()->guard('api')->check() && auth()->guard('api')->user() != null && ($flashSalesProduct != null && $flashSalesProduct->quantity_for_user <= Order::where('user_id', auth('api')->id())->whereNotIn('status', ['client_cancel', 'admin_cancel', 'admin_rejected'])->whereHas('flashSaleOrders', function ($q) use($flashSalesProduct) {
        //             $q->where('flash_sale_product_id', $flashSalesProduct->id);
        //         })->count())) {
        //             $have_sale = null;
        //         } else {
        //             $have_sale = $this->have_sale;
        //         }
        //     }

        // } else {
        //     $have_sale = null;
        // }

        return [
            'id' => (int)$this->id,
            'price' => (float)$this->price,
            'currency' => 'SAR',
            'color' =>  new ColorResource($this->color),
            'size' => new SizeResource($this->size),
            'image'  => $this->image, 
            // 'name' => (string)$this->product->name,
            'quantity' => (float)$this->quantity,

            'is_fav' =>  $fav,
            // 'have_sale'=>  $have_sale,
            'have_sale'=> $this->have_sale,

        ];
    }
}
