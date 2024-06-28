<?php

namespace App\Http\Controllers\Api\Dashboard\NewsLetter;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Dashboard\NewsLetter\NewsLetterResource;
use App\Models\NewsLetter;
use Illuminate\Http\Request;
use Exception;

class NewsLetterController extends Controller
{
   

    public function home() {

        $Item = NewsLetter::latest()->paginate();
        return NewsLetterResource::collection($Item)->additional(['status' => 'success', 'message' => '']);

    }



   
}
