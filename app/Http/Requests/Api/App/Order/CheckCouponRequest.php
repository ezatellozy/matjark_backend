<?php

namespace App\Http\Requests\Api\App\Order;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\{Coupon,Cart};
use App\Traits\OrderOperation;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckCouponRequest extends  ApiMasterRequest
{
    use OrderOperation;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return   true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->code == 'invaild#'){
            
            $data = [
                'code' =>  'required|in:invaild#',
                'guest_token' => 'nullable',
                'address_id' => 'nullable|exists:addresses,id',
    
            ];
            return $data;
        }else{
            $data = [
                'code' =>  'required|exists:coupons,code',
                'guest_token' => 'nullable',
                'address_id' => 'nullable|exists:addresses,id',
    
            ];
            // $coupon = Coupon::where('code', $this->code)->firstOrFail();
            $coupon = Coupon::where('code', $this->code)->first();
            if (!isset($coupon)) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'message' =>  trans('app.messages.coupon.invalid_voucher'),
                        'data' => null,
                    ], 422));
                
            }
            $cart = auth('api')->id() != null ?  Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->first() :  Cart::where(['guest_token' => $this->guest_token, 'user_id' => null])->first();
    
            if ($coupon) {
                $message = $this->CheckCouponIsValide($coupon , null , $cart);
                if ($message  != null) {
                    throw new HttpResponseException(response()->json([
                        'status' => 'fail',
                        'message' =>  $message,
                        'data' => null,
                    ], 422));
                }
            }
            return $data;
        }
    }
}
