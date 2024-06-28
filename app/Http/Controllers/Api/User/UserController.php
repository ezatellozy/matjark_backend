<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\{
    EditProfileRequest ,
    UpdatePasswordRequest
};
use App\Http\Resources\Api\User\{UserProfileResource};
use App\Jobs\UpdateDriverLocation;
use App\Models\{User};
use DB;

class UserController extends Controller
{
	// Show Profile
    public function index()
     {
        $user = auth()->guard('api')->user();
        if (!$user->referral_code) {
            $user->update(['referral_code' => generate_unique_code(8,'\\App\\Models\\User','referral_code','alpha_numbers','lower')]);
        }
        return (new UserProfileResource($user))->additional(['status' => 'success','message'=>'']);
     }


     // Edit Profile
     public function store(EditProfileRequest $request)
    {
        DB::beginTransaction();
        try{
           $user = auth()->guard('api')->user();

           $profile_data = ['country_id','city_id','is_infected'];
           
           if ($user->phone != $request->phone || $user->identity_number != $request->identity_number || $request->date_of_birth_hijri != optional($user->date_of_birth_hijri)->format("Y-m-d") || $request->date_of_birth != optional($user->date_of_birth)->format("Y-m-d")) {
               $update_request_data = ['phone','identity_number','date_of_birth','date_of_birth_hijri'];
               $profile_data = ['country_id','city_id','is_infected'];
               $update_request = $user->updateRequests()->where(['update_status' => 'pending','user_id' => $user->id])->first();
               if ($update_request) {
                   $update_type = in_array($update_request->update_type,['car_data' , 'personal_car_data']) ? 'personal_car_data' : 'personal_data';
                   $update_request->update(array_only($request->validated(),$update_request_data)+['user_type' => $user->user_type , 'update_status' => 'pending' , 'update_type' => $update_type]);
               }else{
                   $user->updateRequests()->create(array_only($request->validated(),$update_request_data)+['user_type' => $user->user_type , 'update_status' => 'pending' , 'update_type' => 'personal_data']);
               }
               $user->update(array_except($request->validated(),array_merge($profile_data,$update_request_data,['health_certificate','phone'])));
               $user->profile()->updateOrCreate(['user_id' => $user->id],array_only($request->validated(),$profile_data));
               $msg = "جاري مراجعة بياناتك من قبل الادارة نظرا لتغيير بعض البيانات الرئيسية";
           }else{
               $user->update(array_except($request->validated(),$profile_data+['health_certificate']));
               $user->profile()->updateOrCreate(['user_id' => $user->id],array_only($request->validated(),$profile_data));
               $msg = "تم التعديل بنجاح";
           }

           if ($request->device_token) {
               $user->devices()->firstOrCreate($request->only(['device_token'])+['type' => 'ios']);
           }
           DB::commit();
           return (new UserProfileResource($user))->additional(['status' => 'success','message'=> $msg]);
        }catch(\Exception $e){
            DB::rollback();
            \Log::info($e->getMessage());
            return response()->json(['status' =>'fail' , 'data' => null ,'message'=>'لم يتم التعديل حاول مرة اخرى'], 401);
        }
     }
     // Edit Password
    public function editPassword(UpdatePasswordRequest $request)
    {
        DB::beginTransaction();
        try{
           $user = auth()->guard('api')->user();
           $user->update(array_only($request->validated(),['password']));
           DB::commit();
           return (new UserProfileResource($user))->additional(['status' => 'success','message'=>"تم التعديل بنجاح"]);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' =>'fail' , 'data' => null ,'message'=>'لم يتم التعديل حاول مرة اخرى'], 401);
        }

    }


    public function updateUserLocation(Request $request)
   {
       $data = $request->all();
       if(isset($data['drivers'])){
           $data = json_decode($data['drivers']);
               UpdateDriverLocation::dispatch($data)->onQueue('high');
       }
       return response()->json(['data' => $request->all()]);
   }

}
