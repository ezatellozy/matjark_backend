<?php

namespace App\Http\Controllers\Api\Dashboard\Offer_Old;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Offer_Old\OfferRequest;
use App\Http\Resources\Api\Dashboard\Offer_Old\{OfferResource, SimpleOfferResource};
use App\Models\Offer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $offers = Offer::when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('desc', '%'.$request->keyword.'%');
        })->when($request->start_at, function ($query) use($request) {
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
        DB::beginTransaction();
        try {
            $offer = Offer::create(array_except($request->validated(), ['product_detail_id']) + ['remain_use' => $request->num_of_use]);
            $offer->offerProductDetails()->attach($request->product_detail_id);

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
            $offer->update(array_except($request->validated(), ['product_detail_id']) + ['remain_use' => $request->num_of_use]);
            $offer->offerProductDetails()->sync($request->product_detail_id);

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
