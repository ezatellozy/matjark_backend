<?php

namespace App\Http\Resources\Api\Website\Home;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleFlashProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $now =Carbon::now();
        return [
            'id' =>(int)$this->id,
            'name' =>(string)$this->name,
            'slug' =>(string)$this->slug,
            'product_details' =>  SimpleProductDetailResource::collection($this->productDetails()->whereHas('flashSalesProduct',function($q) use($now){
                $q->wherehas('flashSale',function($q) use($now){
                    $q->whereDate('start_at', '<=',  $now);
                    $q->whereDate('end_at', '>=',  $now);
                    $q->where('is_active',true);
                }); 
            })->get()),
            'is_fav' =>false,
        ];
    }
}
