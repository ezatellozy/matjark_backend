<?php

namespace App\Http\Resources\Api\App\ReturnOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class ReturnOrderResource extends JsonResource
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
            'id' => (int)$this->id,
            'status' => (string)$this->status,
            'status_trans' =>  (string)trans('app.return_orders.' . $this->status),

            'note' => (string)$this->note,
        ];
    }
}
