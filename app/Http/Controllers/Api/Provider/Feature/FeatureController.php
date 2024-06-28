<?php

namespace App\Http\Controllers\Api\Provider\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Feature\FeatureRequest;
use App\Http\Resources\Api\Provider\Feature\{FeatureResource, FeatureValueResource};
use App\Models\{Feature, FeatureValue, FeatureValueTranslation};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $features = Feature::when($request->keyword, function ($query) use($request) {
            $query->whereTranslationLike('name', $request->keyword);
        })->latest()->paginate();

        return FeatureResource::collection($features)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FeatureRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $feature = Feature::create(array_except($request->validated(), ['values']) + ['added_by_id' => auth('api')->id()]);
            $feature->values()->createMany($request->values);
            $feature->categories()->attach($request->main_category_ids);

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.create.success')]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.create.fail')]);
        }
    }

    public function addValueToFeature(Request $request, $id)
    {
        $feature = Feature::findOrFail($id);

        DB::beginTransaction();
        try
        {
            $feature->values()->createMany($request->values);

            DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('provider.create.success')]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.create.fail')]);
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
        $feature = Feature::findOrFail($id);

        return FeatureResource::make($feature)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FeatureRequest $request, $id)
    {
        $feature = Feature::findOrFail($id);

        DB::beginTransaction();
        try
        {
            $feature->update(array_except($request->validated(), ['values']));

            foreach ($request->values as $value)
            {
                $feature->values()->updateOrCreate(['id' => $value['value_id']], array_except($value, ['value_id']));
            }

            $feature->categories()->sync($request->main_category_ids);

            DB::commit();
            return response()->json(['status' =>'success', 'data' => null,'message' => trans('provider.update.success')]);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider.update.fail')]);
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
        $feature = Feature::findOrFail($id);

        if ($feature->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }

    public function deleteValue($feature, $value)
    {
        $feature_value = FeatureValue::where('feature_id', $feature)->findOrFail($value);

        if ($feature_value->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }
}
