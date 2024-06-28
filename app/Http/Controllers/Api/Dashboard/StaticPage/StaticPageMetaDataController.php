<?php

namespace App\Http\Controllers\Api\Dashboard\StaticPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\StaticPage\StaticPageMetaDataRequest;
use App\Http\Resources\Api\Dashboard\StaticPage\StaticPageMetaDataResource;
use App\Models\StaticPageMetaData;
use Illuminate\Http\Request;
use Exception;

class StaticPageMetaDataController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StaticPageMetaDataRequest $request)
    {
        try {
            $meta_data = StaticPageMetaData::updateOrCreate(['option'=>$request->option],$request->validated());
            return StaticPageMetaDataResource::make($meta_data)->additional(['status' => 'success', 'message' => trans('dashboard.create.success')]);
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
        $meta_data = StaticPageMetaData::find($id);
        return StaticPageMetaDataResource::make($meta_data)->additional(['status' => 'success', 'message' => '']);
    }
}