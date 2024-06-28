<?php

namespace App\Http\Resources\Api\Provider\Wallet;

use App\Http\Resources\Api\Provider\Admin\AdminResource;
use App\Models\User;
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
        $admin = User::whereIn('user_type', ['admin', 'superadmin'])->find($this->added_by);

        return [
            'id'             => (int) $this->id,
            'balance_before' => (float) $this->balance_before,
            'balance_after'  => (float) $this->balance_after,
            'amount'         => (float) $this->amount,
            'type'           => (string) $this->type,
            'bank_data'      => $this->bank_data,
            'order'          => null,
            'transaction_id' => $this->transaction_id ? (string) $this->transaction_id : null,
            'added_by'       => AdminResource::make($admin),
            'create_at'      => $this->created_at->format('Y-m-d'),
        ];
    }
}
