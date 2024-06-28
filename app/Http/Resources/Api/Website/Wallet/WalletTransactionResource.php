<?php

namespace App\Http\Resources\Api\Website\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
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
            'balance_before' => (float)$this->balance_before,
            'balance_after' => (float)$this->balance_after,
            'amount' => (float)$this->amount,
            'type' => (string) $this->type,
            'status'    => $this->status,
            'object' => $this->model_data != null ? $this->model_data : null,
            'create_at' => $this->created_at->format('Y-m-d'),
            'create_time' => $this->created_at->format('H:i a'),
        ];
    }
}
