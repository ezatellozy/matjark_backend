<?php

namespace App\Http\Controllers\Api\Provider\Color;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Color\ColorRequest;
use App\Http\Resources\Api\Provider\Color\ColorResource;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $colors = Color::when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->keyword.'%');
        })->latest()->paginate();

        return ColorResource::collection($colors)->additional(['status' => 'success', 'message' => '']);
    }

    public function getColorsWithoutPagination()
    {
        $colors = Color::latest()->get();
        return ColorResource::collection($colors)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ColorRequest $request)
    {
        $color = Color::create($request->validated() + ['added_by_id' => auth('api')->id()]);

        return (new ColorResource($color))->additional(['status' => 'success', 'message' => trans('provider.create.success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $color = Color::findOrFail($id);

        return (new ColorResource($color))->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ColorRequest $request, $id)
    {
        $color = Color::findOrFail($id);
        $color->update($request->validated());

        return (new ColorResource($color))->additional(['status' => 'success', 'message' => trans('provider.update.success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $color = Color::findOrFail($id);

        if ($color->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.color')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }
}
