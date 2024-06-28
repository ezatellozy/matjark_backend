<?php

namespace App\Http\Controllers\Api\Dashboard\Meta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Meta\MetaRequest;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaSimpleResource;
use App\Models\Meta;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $countries = Country::when($request->keyword, function($q) use($request){
        //     $q->whereTranslationLike('name', '%'.$request->keyword.'%')
        //     ->orWhereTranslationLike('currency', '%'.$request->keyword.'%')
        //     ->orWhereTranslationLike('slug', '%'.$request->keyword.'%');
        // })->latest()->paginate();

        // return CountryResource::collection($countries)->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MetaRequest $request)
    {
        $model = $request->model_type;
        return dd(Category::find(1));
        $row = $model::find($request->model_id);
        
        return dd($row);
        //dd($request->all());
        // $country = Country::create($request->safe()->except(['image']));
        // return CountryResource::make($country)->additional(['status' => 'success', 'message' => trans('dashboard.create.country')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $country = Country::findOrFail($id);
        // return CountryResource::make($country)->additional(['status' => 'success', 'message' => '']);
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
        // $country = Country::findOrFail($id);

        // if($request->phone_code && $request->phone_code != null && $request->phone_code != $country->phonecode)
        // {
        //     User::where('phone_code', $country->phonecode)->update(['phone_code' => $request->phone_code]);
        // }

        // $country->update($request->safe()->except(['image']));

        // return CountryResource::make($country)->additional(['status' => 'success', 'message' => trans('dashboard.update.country')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $country = Country::findOrFail($id);

        // if ($country->delete()) {
        //     return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard.delete.country')]);
        // }

        // return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
