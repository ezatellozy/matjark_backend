<?php

namespace App\Http\Resources\Api\App\Home;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleFlashSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $start_at = Carbon::parse( $this->start_at);
        $end_at = Carbon::parse( $this->end_at);
        $diff = $start_at->diff($end_at)->format('%h:%I:%s');

        return [
            'id' => (int)$this->id,
            // 'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'ends_in' => $diff,
            // 'type' => (string)$this->type,
            // 'type_trans' => (string)$this->type,
            'products' =>  SimpleFlashSaleProductResource::collection($this->flashSaleProducts()->take(5)->get()),


        ];
    }
}
