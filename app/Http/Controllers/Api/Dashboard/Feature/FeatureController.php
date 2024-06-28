<?php

namespace App\Http\Controllers\Api\Dashboard\Feature;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Feature\FeatureRequest;
use App\Http\Resources\Api\Dashboard\Feature\{FeatureResource, FeatureValueResource};
use App\Models\{Category, CategoryFeature, Feature, FeatureValue, FeatureValueTranslation};
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


    public function getFeaturesWithoutPagination(Request $request)
    {
        if($request->category_id && ! empty($request->category_id)) {

            $featuresArr = CategoryFeature::whereIn('category_id' ,$request->category_id)->pluck('feature_id')->toArray(); 

            $features = Feature::whereIn('id',$featuresArr)->when($request->keyword, function ($query) use($request) {
                $query->whereTranslationLike('name', $request->keyword);
            })->latest()->get();

        } else {

            $features = Feature::when($request->keyword, function ($query) use($request) {
                $query->whereTranslationLike('name', $request->keyword);
            })->latest()->get();

        }

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
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.create.success')]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.create.fail')]);
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
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.create.success')]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.create.fail')]);
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

            $updatedArr = [];

            foreach ($request->values as $value)
            {
                $new_feature = $feature->values()->updateOrCreate(['id' => array_key_exists('value_id',$value) ? $value['value_id'] : null], array_except($value, ['value_id']));
                $updatedArr[] = $new_feature->id;
            }

            $currentArr = FeatureValue::where('feature_id',$feature->id)->pluck('id')->toArray();

            //$result_arr = array_merge(array_diff($clients_arr, $clients_contacts_arr), array_diff($clients_contacts_arr, $clients_arr));
            // $result_arr = array_diff($clients_arr, $clients_contacts_arr);

            $result_arr = array_diff($currentArr, $updatedArr);
            FeatureValue::whereIn('id',$result_arr)->delete();

            $feature->categories()->sync($request->main_category_ids);

            // $data['currentArr'] = $currentArr;
            // $data['updatedArr'] = $updatedArr;
            // $data['result_arr'] = $result_arr;

            // DB::commit();
            // return response()->json(['status' =>'success', 'data' => $data,'message' => trans('dashboard.update.success')]);

            DB::commit();
            return response()->json(['status' =>'success', 'data' => null,'message' => trans('dashboard.update.success')]);
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => $exception->getMessage() ], 422);

            // return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.update.fail')], 422);
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
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.delete.fail')], 422);
    }

    public function deleteValue($feature, $value)
    {
        $feature_value = FeatureValue::where('feature_id', $feature)->findOrFail($value);

        if ($feature_value->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard.delete.fail')], 422);
    }
}
