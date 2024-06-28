<?php

namespace App\Http\Resources\Api\App\Help;

use App\Http\Resources\Api\Website\Product\ProductFeaturesResource;
use App\Models\FavouriteProduct;
use App\Models\OrderRate;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Website\Rate\RateResource;
use App\Http\Resources\Api\Website\CommonQuestion\CommonQuestionResource;


class Size3Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        return [
            'id'        => $this->size_id,
            'name'      => (string) @$this->size->name,
            'tag'       => (string) @$this->size->tag,
            'price'      =>(float) $this->price,
            'currency' => 'SAR',
            'quantity'   => $this->quantity,
            'have_sale' =>  $this->have_sale,
            'rate_avg' => (float)$this->rate_avg,

        ];
    }
}