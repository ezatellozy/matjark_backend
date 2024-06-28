<?php

namespace App\Http\Controllers\Api\Provider\FlashSale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\FlashSale\FlashSaleRequest;
use App\Http\Resources\Api\Provider\FlashSale\FlashSaleResource;
use App\Http\Resources\Api\Provider\FlashSale\SimpleFlashSaleResource;
use App\Models\FlashSale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlashSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $flash_sales = FlashSale::when($request->start_at, function ($query) use($request) {
            $query->whereDate('start_at', $request->start_at);
        })->when($request->end_at, function ($query) use($request) {
            $query->whereDate('end_at', $request->end_at);
        })->latest()->paginate();

        return SimpleFlashSaleResource::collection($flash_sales)->additional(['status' => 'success', 'message' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FlashSaleRequest $request)
    {
        DB::beginTransaction();
        try {
            $flash_sale = FlashSale::create(array_except($request->validated(), ['flash_sale_products']));

            $flash_sale->flashSaleProducts()->createMany($request->flash_sale_products);

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.create.success')]);
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.create.fail')], 422);
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
        $flash_sale = FlashSale::findOrFail($id);

        return (new FlashSaleResource($flash_sale))->additional(['status' => 'success', 'message' => null]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FlashSaleRequest $request, $id)
    {
        $flash_sale = FlashSale::findOrFail($id);

        DB::beginTransaction();
        try {
            $flash_sale->update(array_except($request->validated(), ['flash_sale_products']));

            $flash_sale->flashSaleProducts()->whereNotIn('id', collect($request->flash_sale_products)->pluck('id')->filter()->toArray())->delete();

            $flash_sale->flashSaleProducts()->upsert($request->flash_sale_products, ['id']);

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.create.success')]);
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.create.fail')], 422);
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
        $flash_sale = FlashSale::findOrFail($id);

        if ($flash_sale->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }
}
