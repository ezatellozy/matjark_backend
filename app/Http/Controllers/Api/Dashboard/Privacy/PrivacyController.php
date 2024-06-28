<?php

namespace App\Http\Controllers\Api\Dashboard\Privacy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Dashboard\Privacy\PrivacyRequest;
use App\Http\Resources\Api\Dashboard\Privacy\PrivacyResource;
use App\Models\Privacy;
use Exception;

class PrivacyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $privacies = Privacy::latest()->paginate();
        return PrivacyResource::collection($privacies)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PrivacyRequest $request)
    {
        try {
            $privacy = Privacy::create($request->safe()->except('image'));
            return PrivacyResource::make($privacy)->additional(['status' => 'success', 'message' => trans('dashboard.create.success')]);
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
        $privacy = Privacy::findOrFail($id);
        return PrivacyResource::make($privacy)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PrivacyRequest $request, $id)
    {
        $privacy = Privacy::findOrFail($id);
        try {
            $privacy->update($request->safe()->except('image'));
            return PrivacyResource::make($privacy)->additional(['status' => 'success', 'message' => trans('dashboard.update.success')]);
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
        $privacy = Privacy::findOrFail($id);
        if ($privacy->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.success')]);
        }
        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
