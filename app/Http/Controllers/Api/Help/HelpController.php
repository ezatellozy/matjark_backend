<?php

namespace App\Http\Controllers\Api\Help;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\{Help\CarTypeResource};

use App\Models\{CarType};
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function getCarTypes()
    {
        $car_types = CarType::latest()->get();
        return CarTypeResource::collection($car_types)->additional(['status'=>'success','message'=>'']);
    }

}
