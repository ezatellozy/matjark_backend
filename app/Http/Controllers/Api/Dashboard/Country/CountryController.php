<?php

namespace App\Http\Controllers\Api\Dashboard\Country;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Country\CountryRequest;
use App\Http\Resources\Api\Dashboard\City\CityResource;
use App\Http\Resources\Api\Dashboard\City\CitySimpleResource;
use App\Http\Resources\Api\Dashboard\Country\CountryResource;
use App\Http\Resources\Api\Dashboard\Country\CountrySimpleResource;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $countries = Country::when($request->keyword, function($q) use($request){
            $q->whereTranslationLike('name', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('currency', '%'.$request->keyword.'%')
            ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        })->latest()->paginate();

        return CountryResource::collection($countries)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryRequest $request)
    {
        //dd($request->all());
        $country = Country::create($request->safe()->except(['image']));
        return CountryResource::make($country)->additional(['status' => 'success', 'message' => trans('dashboard.create.country')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = Country::findOrFail($id);
        return CountryResource::make($country)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Display the cities in the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCities(Country $country)
    {
        return CityResource::collection($country->cities)->additional(['status' => 'success', 'message' => '']);
    }

    public function getCitiesByCountryWithoutPagination(Country $country)
    {
        return CitySimpleResource::collection($country->cities)->additional(['status' => 'success', 'message' => '']);
    }

    public function getCountriesWithoutPagination()
    {
        $countries = Country::latest()->get();
        return CountrySimpleResource::collection($countries)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CountryRequest $request, $id)
    {
        $country = Country::findOrFail($id);

        if($request->phone_code && $request->phone_code != null && $request->phone_code != $country->phonecode)
        {
            User::where('phone_code', $country->phonecode)->update(['phone_code' => $request->phone_code]);
        }

        $country->update($request->safe()->except(['image']));

        return CountryResource::make($country)->additional(['status' => 'success', 'message' => trans('dashboard.update.country')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);

        if ($country->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.country')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }

    public function statistics($countries)
    {
        $cards['total_countries_count'] = $countries->count();
        $cards['africa_countries_count'] = $countries->where('continent', 'africa')->count() ?? 0;
        $cards['asia_countries_count'] = $countries->where('continent', 'asia')->count() ?? 0;
        $i = 1;
        $data = [];
        foreach ($cards as $k => $v) {
            $data[] = [
                'id'    => $i++,
                'name'  => trans('dashboard/api.statistics.' . $k),
                'value' => $v,
            ];
        }
        return $data;
    }
}
