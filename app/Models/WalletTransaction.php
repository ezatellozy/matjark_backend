<?php

namespace App\Models;

use App\Http\Resources\Api\App\Order\SimpleOrderResource;
use App\Http\Resources\Api\App\User\SimpleUserDataResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function getModelDataAttribute()
    {
        $data = [];
        if ($this->order_id) {
            $orderData =  Order::find($this->order_id) ? new  SimpleOrderResource($this->order) : null;
            $data = [
                'key' => 'order',
                'key_data'      => $orderData
            ];
        } else {
            $data = [
                'key' => 'user',
                'key_data'      => new SimpleUserDataResource(auth()->guard('api')->user())
            ];
        }
        return $data;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

}
