<?php

namespace App\Http\Controllers\Api\App\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\App\General\{AboutResource, PrivacyResource, TermsResource};
use App\Http\Requests\Api\App\General\{ContactRequest};
use App\Http\Resources\Api\Dashboard\Setting\SettingApiResource;
use App\Http\Resources\Api\Dashboard\Setting\SettingResource;
use App\Models\{About, Privacy, Term, Contact};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{

    public function getSetting(Request $request)
    {
        return (new SettingApiResource(null))->additional(['status' => 'success', 'message' => '']);
    }

    public function getAbout()
    {
        $about =   About::orderBy('ordering', 'asc')->get();
        return (AboutResource::collection($about))->additional(['status' => 'success', 'message' => '']);
    }
    public function getTerms()
    {
        $terms =   Term::orderBy('ordering', 'asc')->get();
        return (TermsResource::collection($terms))->additional(['status' => 'success', 'message' => '']);
    }
    public function getPrivacy()
    {
        $privacy = Privacy::orderBy('ordering', 'asc')->get();
        return (PrivacyResource::collection($privacy))->additional(['status' => 'success', 'message' => '']);
    }

    public function getContact()
    {
        $data = [
            'lat' => (string)setting('lat'),
            'lng' => (string)setting('lng'),
            'description_location' => (string)setting('description_location'),
            'phone' => (string)setting('phone'),
            'email' => (string)setting('email'),
            'whatsapp' => (string)setting('whatsapp'),
            'ios_link' => (string)setting('ios_link'),
            'android_link' => (string)setting('android_link'),
            'website_link' => (string)setting('website_link'),
            'social' => [
                'facebook' => (string)setting('facebook'),
                'twitter' => (string)setting('twitter'),
                'instagram' => (string)setting('instagram'),
                'youtube' => (string)setting('youtube'),
                'pinterest' => (string)setting('pinterest'),
                'twitter' => (string)setting('twitter'),
            ],
        ];
        return response()->json(['data' => $data, 'status' => 'success', 'message' => '']);
    }

    public function contact(ContactRequest $request)
    {
        $contact = Contact::create($request->validated());
        // $admins = User::whereIn('user_type',['admin','superadmin'])->get();
        // \Notification::send($admins,new ContactNotification($contact));
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.messages.send_successfully')]);
    }
    public function howToShop()
    {
        $how_to_shop = app()->getLocale() == 'ar' ? 'how_to_shop_ar' : 'how_to_shop_en';
        return response()->json(['data' => ['how_to_shop' => setting($how_to_shop) != false ? setting($how_to_shop) : ''], 'status' => 'success', 'message' => '']);
    }
    public function returnPolicy()
    {
        $return_policy = app()->getLocale() == 'ar' ? 'return_policy_ar' : 'return_policy_en';
        return response()->json(['data' => ['return_policy' => setting($return_policy) != false ? setting($return_policy) : ''], 'status' => 'success', 'message' => '']);
    }
    public function bostaTest()
    {
        $test = Http::withHeaders([
            'Content-Type'  => 'application/json',
            // 'Authorization'=> 'Bearer f9db51b3390c323c8b2d6ff3a1d0fb2da3aff2b1436db3b447c268d4a098403c',

            "Authorization" => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IlAzWVNRNjF3Znp3UVRqY09jY0pXSCIsInJvbGVzIjpbIkJVU0lORVNTX0FETUlOIl0sImJ1c2luZXNzQWRtaW5JbmZvIjp7ImJ1c2luZXNzSWQiOiI3dEJIMGhYY3p2YVlHMW14Y2dmU1QiLCJidXNpbmVzc05hbWUiOiLYr9in2YHZitmG2KctIERhdmluYSJ9LCJjb3VudHJ5Ijp7Il9pZCI6IjYwZTQ0ODJjN2NiN2Q0YmM0ODQ5YzRkNSIsIm5hbWUiOiJFZ3lwdCIsIm5hbWVBciI6ItmF2LXYsSIsImNvZGUiOiJFRyJ9LCJlbWFpbCI6ImtobG9vZHN3aWRhbkBnbWFpbC5jb20iLCJwaG9uZSI6IjAxMTExOTczNzI0Iiwic2Vzc2lvbklkIjoiaUpMUlJoRjhmWFFGUm10UjdONWpmIiwiaWF0IjoxNjcxNjk5NDE0LCJleHAiOjE2NzI5MDkwMTR9.ITS6PFq3MzMCOPWqfrozZL_ZLrMLZ6viPFxaG7Hs49g",

        ])->post('http://app.bosta.co/api/v2/deliveries', [

            "type" => 10,
            "specs" => [
                "packageType" => "Parcel",
                "packageDetails" => [
                    "itemsCount" => 5,
                    "description" => "Desc."
                ]
            ],
            "notes" => "Welcome Note",
            "cod" => 50,
            "dropOffAddress" => [
                "districtId" => "Iy7-lFD0BE0",
                "city" => "Red Sea",
                "zone" => "Qesm Hurghada 2",
                "district" => "Qesm Hurghada 2",
                "firstLine" => "Maadi",
                "secondLine" => "Nasr  City",
                "buildingNumber" => "123",
                "floor" => "4",
                "apartment" => "2"
            ],
            "pickupAddress" => [
                "city" => "Red Sea",
                "zone" => "Qesm Hurghada 2",
                "district" => "Qesm Hurghada 2",
                "firstLine" => "Maadi",
                "secondLine" => "Nasr  City",
                "buildingNumber" => "123",
                "floor" => "4",
                "apartment" => "2"
            ],
            "returnAddress" => [
                "city" => "Red Sea",
                "zone" => "Qesm Hurghada 2",
                "district" => "Qesm Hurghada 2",
                "firstLine" => "Maadi",
                "secondLine" => "Nasr  City",
                "buildingNumber" => "123",
                "floor" => "4",
                "apartment" => "2"
            ],
            "businessReference" => "43535252",
            "receiver" => [
                "firstName" => "Sasuke",
                "lastName" => "Uchiha",
                "phone" => "01065685435",
                "email" => "ahmed@ahmed.com"
            ],
            "webhookUrl" => "https://www.google.com/"


        ]);
        dd($test);

        return response()->json(['data' => $test, 'status' => 'sucess', 'message' => '']);
    }
    public function cities()
    {
        $test = Http::withHeaders([
            'Content-Type'  => 'application/json',
            "Authorization" => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IlAzWVNRNjF3Znp3UVRqY09jY0pXSCIsInJvbGVzIjpbIkJVU0lORVNTX0FETUlOIl0sImJ1c2luZXNzQWRtaW5JbmZvIjp7ImJ1c2luZXNzSWQiOiI3dEJIMGhYY3p2YVlHMW14Y2dmU1QiLCJidXNpbmVzc05hbWUiOiLYr9in2YHZitmG2KctIERhdmluYSJ9LCJjb3VudHJ5Ijp7Il9pZCI6IjYwZTQ0ODJjN2NiN2Q0YmM0ODQ5YzRkNSIsIm5hbWUiOiJFZ3lwdCIsIm5hbWVBciI6ItmF2LXYsSIsImNvZGUiOiJFRyJ9LCJlbWFpbCI6ImtobG9vZHN3aWRhbkBnbWFpbC5jb20iLCJwaG9uZSI6IjAxMTExOTczNzI0Iiwic2Vzc2lvbklkIjoiaUpMUlJoRjhmWFFGUm10UjdONWpmIiwiaWF0IjoxNjcxNjk5NDE0LCJleHAiOjE2NzI5MDkwMTR9.ITS6PFq3MzMCOPWqfrozZL_ZLrMLZ6viPFxaG7Hs49g",

        ])->get('http://app.bosta.co/api/v2/cities');
        return $test;
    }
    public function districts()
    {
        $districts = Http::withHeaders([
            'Content-Type'  => 'application/json',
            "Authorization" => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IlAzWVNRNjF3Znp3UVRqY09jY0pXSCIsInJvbGVzIjpbIkJVU0lORVNTX0FETUlOIl0sImJ1c2luZXNzQWRtaW5JbmZvIjp7ImJ1c2luZXNzSWQiOiI3dEJIMGhYY3p2YVlHMW14Y2dmU1QiLCJidXNpbmVzc05hbWUiOiLYr9in2YHZitmG2KctIERhdmluYSJ9LCJjb3VudHJ5Ijp7Il9pZCI6IjYwZTQ0ODJjN2NiN2Q0YmM0ODQ5YzRkNSIsIm5hbWUiOiJFZ3lwdCIsIm5hbWVBciI6ItmF2LXYsSIsImNvZGUiOiJFRyJ9LCJlbWFpbCI6ImtobG9vZHN3aWRhbkBnbWFpbC5jb20iLCJwaG9uZSI6IjAxMTExOTczNzI0Iiwic2Vzc2lvbklkIjoiaUpMUlJoRjhmWFFGUm10UjdONWpmIiwiaWF0IjoxNjcxNjk5NDE0LCJleHAiOjE2NzI5MDkwMTR9.ITS6PFq3MzMCOPWqfrozZL_ZLrMLZ6viPFxaG7Hs49g",

        ])->get('http://app.bosta.co/api/v2/cities/getAllDistricts');
        return $districts;
    }
    public function districtsByCityId($id)
    {
        $districts = Http::withHeaders([
            'Content-Type'  => 'application/json',
            "Authorization" => "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IlAzWVNRNjF3Znp3UVRqY09jY0pXSCIsInJvbGVzIjpbIkJVU0lORVNTX0FETUlOIl0sImJ1c2luZXNzQWRtaW5JbmZvIjp7ImJ1c2luZXNzSWQiOiI3dEJIMGhYY3p2YVlHMW14Y2dmU1QiLCJidXNpbmVzc05hbWUiOiLYr9in2YHZitmG2KctIERhdmluYSJ9LCJjb3VudHJ5Ijp7Il9pZCI6IjYwZTQ0ODJjN2NiN2Q0YmM0ODQ5YzRkNSIsIm5hbWUiOiJFZ3lwdCIsIm5hbWVBciI6ItmF2LXYsSIsImNvZGUiOiJFRyJ9LCJlbWFpbCI6ImtobG9vZHN3aWRhbkBnbWFpbC5jb20iLCJwaG9uZSI6IjAxMTExOTczNzI0Iiwic2Vzc2lvbklkIjoiaUpMUlJoRjhmWFFGUm10UjdONWpmIiwiaWF0IjoxNjcxNjk5NDE0LCJleHAiOjE2NzI5MDkwMTR9.ITS6PFq3MzMCOPWqfrozZL_ZLrMLZ6viPFxaG7Hs49g",

        ])->get('http://app.bosta.co/api/v2/cities/' . $id . '/districts');
        return $districts;
    }
}
