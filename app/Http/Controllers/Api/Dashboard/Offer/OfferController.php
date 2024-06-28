<?php

namespace App\Http\Controllers\Api\Dashboard\Offer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Offer\OfferRequest;
use App\Http\Resources\Api\Dashboard\Offer\OfferItemResource;
use App\Http\Resources\Api\Dashboard\Offer\OfferResource;
use App\Http\Resources\Api\Dashboard\Offer\SimpleOfferResource;
use App\Models\DiscountOfOffer;
use App\Models\Offer;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{

    public function getActiveOffers(Request $request)
    {
        $now  = Carbon::now();

        $offers = Offer::where('is_active', true)->where('remain_use', '>', 0)->where('start_at', '<=',  $now)->where('end_at', '>=',  $now)->latest()->get();

        return OfferItemResource::collection($offers)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $to   = Carbon::now()->endOfMonth()->format('Y-m-d');

        $offers = Offer::when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('desc', '%'.$request->keyword.'%');
        })

        ->when($request->get_data_by != null, function ($query) use($request,$from , $to) {

            if($request->get_data_by == 'this_month') {
                $query->whereBetween('start_at', [$from , $to]);
            }

        })

        ->when($request->start_at, function ($query) use($request) {
            $query->whereDate('start_at', $request->start_at);
        })->when($request->end_at, function ($query) use($request) {
            $query->whereDate('end_at', $request->end_at);
        })->latest()->paginate();

        return SimpleOfferResource::collection($offers)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OfferRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $offer = Offer::create(array_except($request->validated(), ['buy_to_get', 'discount_of_offers']) + ['remain_use' => $request->num_of_use]);

            if ($request->type == 'fix_amount' or $request->type == 'percentage')
            {
                $offer->discountOfOffer()->create($request->discount_of_offers);
            }
            elseif ($request->type == 'buy_x_get_y')
            {
                $offer->buyToGetOffer()->create($request->buy_to_get);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.create.success')]);
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.create.fail')], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $offer = Offer::findOrFail($id);

        return (new OfferResource($offer))->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OfferRequest $request, $id)
    {
        $offer = Offer::findOrFail($id);

        DB::beginTransaction();
        try {

            $offer->update(array_except($request->validated(), ['buy_to_get', 'discount_of_offers']) + ['remain_use' => $request->num_of_use]);

            if ($request->type == 'fix_amount' or $request->type == 'percentage')
            {
                // info(11111);
                // info($offer->discountOfOffer()->toArray());
                // info($request->discount_of_offers);

                // $offer->discountOfOffer()->update( $request->discount_of_offers);

                if($offer->discountOfOffer != null) {
                    $offer->discountOfOffer()->update( $request->discount_of_offers);
                } else {
                    // $offer->discountOfOffer()->create($request->discount_of_offers);
                    DiscountOfOffer::create(['offer_id' => $offer->id] + $request->discount_of_offers);
                }
            }
            elseif ($request->type == 'buy_x_get_y')
            {
                // $offer->buyToGetOffer()->update($request->buy_to_get);

                if($offer->buyToGetOffer != null) {
                    $offer->buyToGetOffer()->update( $request->buy_to_get);
                } else {
                    $offer->buyToGetOffer()->create($request->buy_to_get);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.update.success')]);
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.update.fail')], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);

        if ($offer->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
