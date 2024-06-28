<?php

namespace App\Http\Resources\Api\Provider\Wallet;

use App\Http\Resources\Api\Provider\Admin\AdminResource;
use App\Http\Resources\Api\Provider\Client\ClientResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $admin = User::whereIn('user_type', ['admin', 'superadmin'])->find($this->admin_id);

        return [
            'id'              => (int) $this->id,
            'bank_name'       => (string) $this->bank_name,
            'branch'          => (string) $this->branch,
            'account_number'  => (string) $this->account_number,
            'iban'            => (string) $this->iban,
            'city'            => (string) $this->city,
            'status'          => (string) $this->status,
            'amount'          => (float) $this->amount,
            'currency'        => (string) $this->currency,
            'rejected_reason' => (string) $this->rejected_reason,
            'client'          => ClientResource::make($this->user),
            'admin'           => AdminResource::make($admin),
            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
