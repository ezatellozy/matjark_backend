<?php

namespace App\Http\Controllers\Api\Dashboard\About;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\About\AboutMetaDataRequest;
use App\Http\Resources\Api\Dashboard\About\AboutMetaDataResource;
use App\Models\AboutMetaData;
use Illuminate\Http\Request;
use Exception;

class AboutMetaDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $about_meta = AboutMetaData::latest()->get();
        return AboutMetaDataResource::collection($about_meta)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AboutMetaDataRequest $request)
    {
        try {
            $meta_data = AboutMetaData::updateOrCreate(['id'=>1],$request->validated());
            return AboutMetaDataResource::make($meta_data)->additional(['status' => 'success', 'message' => trans('dashboard.create.success')]);
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
        $about_meta = AboutMetaData::find($id);
        return AboutMetaDataResource::make($about_meta)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AboutRequest $request, AboutMetaData $aboutMetaData)
    {
        try {
             $aboutMetaData->update($request->validated());
            return AboutMetaDataResource::make($about)->additional(['status' => 'success', 'message' => trans('dashboard.update.success')]);
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
    public function destroy(AboutMetaData $aboutMetaData)
    {

        if ($aboutMetaData->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
