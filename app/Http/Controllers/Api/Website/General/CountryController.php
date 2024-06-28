<?php

namespace App\Http\Controllers\Api\Website\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Help\{CountryResource , CityResource};
use App\Models\{Country , City};
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::latest()->get();
        return CountryResource::collection($countries)->additional(['status' => 'success','message'=>'']);
    }

    public function show($id)
    {
        $cities = City::where('country_id',$id)->latest()->get();
        return CityResource::collection($cities)->additional(['status' => 'success','message'=>'']);
    }
}
