<?php

namespace App\Http\Resources\Api\Website\Cart;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{Offer,CartOfferType};

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $offerStatus = '';

        $cartOfferBuy = CartOfferType::where(['type' => 'buy_x', 'cart_id' => $this->id])->first();
        $cartOfferGet = CartOfferType::where(['type' => 'get_y', 'cart_id' => $this->id])->first();

        if ($cartOfferBuy != null  && $cartOfferBuy->offer->buyToGetOffer->buy_quantity >  $cartOfferBuy->quantity) {
            $offerStatus = 'buy';
        } elseif (($cartOfferGet != null  && $cartOfferBuy->offer->buyToGetOffer->get_quantity >  $cartOfferGet->quantity  ) || ($cartOfferGet == null &&  $cartOfferBuy != null)) {
            $offerStatus = 'get';
        } else {
            $offerStatus = 'finished';
        }
        return [
            'id' =>(int)$this->id,
            'offer_id' => $cartOfferBuy != null? $cartOfferBuy->offer_id : null, 
            'offer_status' =>  $offerStatus,
            'items' => CartProductResource::collection($this->cartProducts),
        ];
    }
}
