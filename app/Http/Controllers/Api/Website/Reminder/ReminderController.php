<?php

namespace App\Http\Controllers\Api\Website\Reminder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Website\Reminder\ReminderRequest;
use App\Http\Resources\Api\Website\Product\SimpleProductDetailsResource;
use App\Models\{FlashSaleProduct, ProductDetails, Reminder};
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $products =   ProductDetails::whereHas('flashSalesProduct', function ($q) {
            $q->whereHas('reminderFlashSales', function ($q) {
                $q->where(['user_id' => auth('api')->id(), 'status' => 'wait']);
            });
        })->paginate(6);
        return (SimpleProductDetailsResource::collection($products))->additional(['status' => 'success', 'message' => '']);
    }
    public function reminder(ReminderRequest $request)
    {
        if (Reminder::where(['user_id' => auth('api')->id(), 'flash_sale_product_id' => $request->flash_sale_product_id, 'status' => 'wait'])->exists()) {
            Reminder::where(['user_id' => auth('api')->id(), 'flash_sale_product_id' => $request->flash_sale_product_id, 'status' => 'wait'])->delete();
            $is_reminder = false;
        } else {
            $flashSaleProduct = FlashSaleProduct::find($request->flash_sale_product_id);
            Reminder::create(['user_id' => auth('api')->id(),  'flash_sale_product_id' => $request->flash_sale_product_id, 'status' => 'wait', 'start_flash_sale_date' => $flashSaleProduct->flashSale->start_at, 'time_in_minute' => setting('time_of_reminder_in_minute')]);
            $is_reminder = true;
        }
        return response()->json(['data' => ['is_reminder' => $is_reminder], 'status' => 'success', 'message' => trans('app.messages.edited_successfully')]);
    }
}
