<?php

namespace App\Http\Resources\Api\Website\Home;

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
        $diff = $start_at->diffInSeconds($end_at);
        return [
            'id' => (int)$this->id,
            // 'start_at' => $this->start_at,
            'end_at' =>  $this->end_at,
            'end_at_for_web' =>  $this->end_at != null ? Carbon::parse($this->end_at)->format('Y-m-d H:i:s') : null,
            'ends_in' => $diff,
            // 'type' => (string)$this->type,
            // 'type_trans' => (string)$this->type,
            'products' =>  SimpleFlashSaleProductResource::collection($this->flashSaleProducts()->take(5)->get()),
        ];
    }
}
