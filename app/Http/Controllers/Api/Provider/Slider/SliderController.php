<?php

namespace App\Http\Controllers\Api\Provider\Slider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Slider\SliderRequest;
use App\Http\Resources\Api\Provider\Slider\SliderResource;
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
            $slider = Slider::create($request->safe()->except('image'));

            return SliderResource::make($slider)->additional(['status' => 'success', 'message' => trans('provider.create.success')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.create.fail')], 422);
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
            $slider->update($request->safe()->except('image'));

            return SliderResource::make($slider)->additional(['status' => 'success', 'message' => trans('provider.update.success')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.update.fail')], 422);
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
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }
}
