<?php

namespace App\Http\Controllers\Api\Website\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Website\General\{AboutResource, PrivacyResource, TermsResource};
use App\Http\Requests\Api\Website\General\{ContactRequest};
use App\Models\{About, Privacy, Term ,Contact};

use Illuminate\Http\Request;

class SettingController extends Controller
{
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
}
