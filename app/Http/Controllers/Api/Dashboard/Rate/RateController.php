<?php

namespace App\Http\Controllers\Api\Dashboard\Rate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Rate\ChangeRateSatusRequest;
use App\Http\Resources\Api\Dashboard\Rate\RateResource;
use App\Models\Order;
use App\Models\OrderRate;
use App\Models\RateImages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RateController extends Controller
{

    public function rated_users() {

        $users = User::whereHas('orders')->get(['id','fullname']);

        return response()->json([
            'status' => 'success', 
            'data' => $users, 
            'messages' => ''
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $rates = OrderRate::when($request->type == 'rate', function ($query) use($request) {
        //     $query->where('id', $request->id);
        // })->when($request->type == 'order', function ($query) use($request) {
        //     $query->where('order_id', $request->id);
        // })->when($request->type == 'user', function ($query) use($request) {
        //     $query->where('user_id', $request->id);
        // })->paginate();
        
        $rates = OrderRate::when($request->status, function ($query) use($request) {
            $query->where('status', $request->status);
        })->when($request->user_id, function ($query) use($request) {
            $query->where('user_id', $request->user_id);
        })
        ->when($request->product_id, function ($query) use ($request) {
            $query->whereHas('product',function($product) use ($request) {
                $product->where('id',$request->product_id);
            });
        })
        ->paginate();
        
        return RateResource::collection($rates)->additional(['status' => 'success', 'message' => '']);
    }

    public function changeStatus(ChangeRateSatusRequest $request)
    {
        if (is_array($request->apply_on))
        {
            if($request->status == 'accepted') {

                $data = OrderRate::whereIn('id', $request->apply_on)->get();

                foreach($data as $rate) {

                    $avg = rate_avg($rate->product_detail_id);

                    $rate->productDetail()->update([
                        'rate_avg' => $avg,
                    ]);

                    $rate->update(['status' => $request->status, 'reject_reason' => $request->reject_reason]);
                }

            } else {
                OrderRate::whereIn('id', $request->apply_on)->update(['status' => $request->status, 'reject_reason' => $request->reject_reason]);
            }
        }
        elseif ($request->apply_on == 'all')
        {
            OrderRate::where('status', 'pending')->update(['status' => $request->status, 'reject_reason' => $request->reject_reason]);
        }

        return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.update.successfully')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order_rate = OrderRate::findOrFail($id);
        return (new RateResource($order_rate))->additional(['status' => 'success', 'message' => '']);
    }

    public function deleteImage($rate, $image)
    {
        $rate_image = RateImages::where('rate_id', $rate)->findOrFail($image);
        if (file_exists(storage_path('app/public/images/rateImages/'.$rate_image->media))){
            File::delete(storage_path('app/public/images/rateImages/'.$rate_image->media));
        }
        $rate_image->delete();
        return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rate = OrderRate::findOrFail($id);
        $rate->productDetail->update(['rate_avg' => rate_avg($rate->product_detail_id)]);

        if ($rate->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.size')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
