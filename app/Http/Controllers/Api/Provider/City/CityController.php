<?php

namespace App\Http\Controllers\Api\Provider\City;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\City\CityRequest;
use App\Http\Resources\Api\Provider\City\CityResource;
use App\Http\Resources\Api\Provider\City\CitySimpleResource;
use App\Http\Resources\Api\Provider\District\DistrictResource;
use App\Http\Resources\Api\Provider\District\DistrictSimpleResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cities = City::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        })->latest()->paginate(10);

        $statistics = $this->statistics($request);

        return CityResource::collection($cities)->additional(['status' => 'success', 'message' => '', 'statistics' => $statistics]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
        $city = City::create($request->validated());

        return CityResource::make($city->fresh())->additional(['status' => 'success', 'message' => trans('provider.create.city')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $city = City::findOrFail($id);

        return CityResource::make($city)->additional(['status' => 'success', 'message' => '']);
    }

    public function getCitiesWithoutPagination()
    {
        $cities = City::latest()->get();
        return CitySimpleResource::collection($cities)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CityRequest $request, $id)
    {
        $city = City::findOrFail($id);
        $city->update($request->validated());

        return CityResource::make($city)->additional(['status' => 'success', 'message' => trans('provider.update.city')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);

        if ($city->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.city')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail')], 422);
    }

    public function statistics($request)
    {
        $cities = City::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        });
        $cards['total_cities_count'] = $cities->count();
        $cards['africa_cities_count'] = $cities->whereHas('country', function($q){
            $q->where('continent', 'africa');
        })->count() ?? 0;
        $cards['asia_cities_count'] = $cities->whereHas('country', function($q){
            $q->where('continent', 'asia');
        })->count() ?? 0;
        $i = 1;
        $data = [];
        foreach ($cards as $k => $v) {
            $data[] = [
                'id'    => $i++,
                'name'  => trans('provider.statistics.' . $k),
                'value' => $v,
            ];
        }
        return $data;
    }
}
