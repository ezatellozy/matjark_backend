<?php

namespace App\Http\Controllers\Api\App\RecentSearch;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\App\RecentSearch\RecentSearchResource;
use App\Models\{RecentSearches};
use Illuminate\Http\Request;

class RecentSearchController extends Controller
{
    public function index(Request $request)
    {

        if(auth()->guard('api')->user() != null){

            $recentSearches =   RecentSearches::where('user_id', auth('api')->id())->latest()->get();
        }else{
            $recentSearches =   RecentSearches::where('guest_token', $request->guest_token)->latest()->get();

        }
        return (RecentSearchResource::collection($recentSearches))->additional(['status' => 'success', 'message' => '']);
    }


    public function deleteAll(Request $request)
    {
        try {
            if(auth()->guard('api')->user() != null){

                $recentSearches =   RecentSearches::where('user_id', auth('api')->id())->delete();
            }else{
                $recentSearches =   RecentSearches::where('guest_token', $request->guest_token)->delete();
            }
            return response()->json(['data' =>null, 'status' => 'success', 'message' => trans('app.messages.deleted_successfully')]);

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }

    public function destroy(Request $request, $id)
    {
        $recentSearch = RecentSearches::findOrFail($id);
        try {
            $recentSearch->delete();
            if(auth()->guard('api')->user() != null){

                $recentSearches =   RecentSearches::where('user_id', auth('api')->id())->latest()->get();
            }else{
                $recentSearches =   RecentSearches::where('guest_token', $request->guest_token)->latest()->get();
    
            }
            return (RecentSearchResource::collection($recentSearches))->additional(['status' => 'success', 'message' => trans('app.messages.deleted_successfully')]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }
}
