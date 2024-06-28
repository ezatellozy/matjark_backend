<?php

namespace App\Http\Controllers\Api\Dashboard\Slider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Slider\SliderRequest;
use App\Http\Resources\Api\Dashboard\Slider\SliderResource;
use App\Models\Slider;
use Exception;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Slider::latest()->paginate();

        return SliderResource::collection($sliders)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SliderRequest $request)
    {
        try {

            $arr = $request->safe()->except(['image','item_id']);

            if(request()->item_type == 'products') {
                $arr['item_id'] = json_encode($request->item_id);
            } else {
                $arr['item_id'] = $request->item_id;
            }

            $slider = Slider::create($arr);

            return SliderResource::make($slider)->additional(['status' => 'success', 'message' => trans('dashboard.create.success')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.create.fail')], 422);
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
        $slider = Slider::findOrFail($id);

        return SliderResource::make($slider)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SliderRequest $request, $id)
    {
        try {
            $slider = Slider::findOrFail($id);

            $arr = $request->safe()->except('image','item_id');

            if(request()->item_type == 'products') {
                $arr['item_id'] = json_encode($request->item_id);
            } else {
                $arr['item_id'] = $request->item_id;
            }

            $slider->update($arr);

            return SliderResource::make($slider)->additional(['status' => 'success', 'message' => trans('dashboard.update.success')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.update.fail')], 422);
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
        $slider = Slider::findOrFail($id);

        if ($slider->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
