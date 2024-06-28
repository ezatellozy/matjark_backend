<?php

namespace App\Http\Controllers\Api\Provider\Term;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Term\TermRequest;
use App\Http\Resources\Api\Provider\Term\TermResource;
use App\Models\Term;
use Exception;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $terms = Term::latest()->paginate();
        return TermResource::collection($terms)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TermRequest $request)
    {
        try {
            $term = Term::create($request->safe()->except('image'));
            return TermResource::make($term)->additional(['status' => 'success', 'message' => trans('provider.create.success')]);
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
        $term = Term::findOrFail($id);
        return TermResource::make($term)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TermRequest $request, $id)
    {
        try {
            $term = Term::findOrFail($id);

            $term->update($request->safe()->except('image'));
            return TermResource::make($term)->additional(['status' => 'success', 'message' => trans('provider.update.success')]);
        } catch (Exception $e) {
            info($e);
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
        $term = Term::findOrFail($id);
        if ($term->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
        }
        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }
}
