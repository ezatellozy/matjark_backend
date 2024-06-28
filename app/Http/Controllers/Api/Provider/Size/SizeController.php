<?php

namespace App\Http\Controllers\Api\Provider\Size;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Size\SizeRequest;
use App\Http\Resources\Api\Provider\Size\SizeResource;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sizes = Size::when($request->category_id, function ($query) use($request) {

            $query->whereHas('categorySizes' , function ($query) use($request) {

                $query->where('category_id', $request->category_id);
            });
        })->when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', '%'.$request->keyword.'%');
        })->latest()->paginate();

        return SizeResource::collection($sizes)->additional(['status' => 'success', 'message' => '']);
    }

    public function getSizesWithoutPagination()
    {
        $sizes = Size::latest()->get();
        return SizeResource::collection($sizes)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SizeRequest $request)
    {
        $size = Size::create(array_except($request->validated(), ['main_category_ids']) + ['added_by_id' => auth('api')->id()]);
        $size->categories()->attach($request->main_category_ids);

        return (new SizeResource($size))->additional(['status' => 'success', 'message' => trans('provider.create.success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $size = Size::findOrFail($id);

        return (new SizeResource($size))->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SizeRequest $request, $id)
    {
        $size = Size::findOrFail($id);
        $size->update(array_except($request->validated(), ['main_category_ids']));
        $size->categories()->sync($request->main_category_ids);

        return SizeResource::make($size)->additional(['status' => 'success', 'message' => trans('provider.update.success')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $size = Size::findOrFail($id);

        if ($size->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.size')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }
}
