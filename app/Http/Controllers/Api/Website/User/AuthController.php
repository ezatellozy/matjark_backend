<?php

namespace App\Http\Controllers\Api\Website\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Website\User\{CheckCodeRequest, VerifyCodeRequest, AddPhoneRequest, CheckPhoneCodeRequest, EditPhoneRequest, EditProfileRequest, ForgetPasswordRequest, LoginRequest, LogoutRequest, RegisterRequest, ResendCodeRequest, ResetPasswordRequest, UpdateLangRequest, UpdatePasswordRequest, UpdatePhoneRequest, VerifyRequest};
use App\Http\Resources\Api\App\User\{UserResource};
use App\Models\{Country, Profile, User, Device, Phone, Wallet};
use App\Traits\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyUserMail;
use App\Notifications\Api\User\NewUserNotification;

class AuthController extends Controller
{
    use Cart;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verify', 'resendCode', 'forgotPassword', 'checkCode', 'resetPassword']]);
    }


    // register
    public function register(RegisterRequest $request)
    {
        \DB::beginTransaction();
        try {

            $code = 1111;
            // $code = mt_rand(1111, 9999);
            // if (setting('use_sms_service') == 'enable') {
            //     $code = mt_rand(1111, 9999); //generate_unique_code(4,'\\App\\Models\\User','verified_code');
            // }


            $user_data = [
                'verified_code' => $code,
                'is_active' => 0,
                'is_admin_active_user' => 1,
                'user_type' => 'client',
            ];
            $user = User::create(array_except($request->validated(), $request->profile_date) + $user_data);

            // $code = mt_rand(1111, 9999);
            // Mail::to($user->email)->send(new VerifyUserMail($user, $code));

            $user->profile()->create($request->profile_date + ['added_by_id' => auth('api')->id()]);
            Wallet::create(['user_id' => $user->id]);
            $msg =  $code;
            // send_sms($user->phone, $msg);
            \DB::commit();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.success_sign_up'), 'dev_message' => $user->verified_code]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.auth.not_registered_try_again')], 422);
        }
    }

    
    // verify code
    public function verify(VerifyRequest $request)
    {

        $lang = app()->getLocale();

        DB::beginTransaction();

        try {

            // $user = User::where(function($query) use($request) {
            //     $query->where('email',$request->phone)->orWhere('phone',$request->phone);
            // })->where('user_type','client')->first();

            $user = User::where([ 'phone' => $request->phone, 'phone_code' => $request->phone_code, 'user_type' => 'client'])->first();

            if($user != null) {

                $check_user = User::where(['id' => $user->id,'verified_code' => $request->code])->first();

                if($check_user != null) {

                    if($check_user->phone_verified_at == null ) {

                        if ($user->verified_code == $request->code) {

                            $user->update(['is_active' => true, 'verified_code' => null, 'phone_verified_at' => now()]);

                            $user->devices()->firstOrCreate($request->only(['device_token', 'type']));

                            $token = \JWTAuth::fromUser($user);

                            data_set($user, 'token', $token);

                            //send notification to admin

                            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();

                            if ($admins) {
                                foreach ($admins as $admin) {
                                    $admin->notify(new NewUserNotification($user, ['database', 'fcm']));
                                }
                            }

                            \DB::commit();
                            return response()->json(['status' => 'success', 'message' => '', 'data' => new UserResource($user)], 200);

                        } else {
                            return response()->json(['status' => 'fail', 'message' => trans('app.auth.wrong_code_please_try_again'), 'data' => null], 422);
                        }

                    } else {
                        $msg = $lang == 'en' ? ' this user is already verified' : 'تم التحقق من هذا المستخدم بالفعل';
                        return response()->json(['status' => 'success', 'data' => null, 'message' => $msg ], 422);
                    }
                } else {
                    $msg = $lang == 'en' ? 'sorry this code is invalid' : 'عفوا هذا الكود غير صحيح';
                    return response()->json(['status' => 'fail', 'data' => null, 'message' => $msg ], 422);
                }

            } else {
                $msg = $lang == 'en' ? 'user not found' : 'عفوا المستخدم غير موجود';
                return response()->json(['status' => 'fail', 'data' => null, 'message' => $msg ], 422);
            }


        } catch (\Exception $e) {
            DB::rollback();
            //dd($e->getMessage());
            Log::info($e->getLine());
            Log::info($e->getMessage());
            Log::info($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }


    public function resendCode(ResendCodeRequest $request)
    {
        $user = User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code])->firstOrFail();

        // $user = User::where(function($query) use($request) {

        //     $query->where('email',$request->phone)->orWhere('phone',$request->phone);

        // })->when($request->phone_code,function($query) use($request) {

        //     $query->where('phone_code',$request->phone_code);

        // })->firstOrFail();

        try {

            if ($request->type == 'register') {

                // $verification_type = setting('verification_type');
                $verification_type = 'phone';

                if ($verification_type && $verification_type == 'email') {

                    // $user->notify(new VerifyApiMail(['mail']));

                    // $code = mt_rand(1111, 9999);
                    // Mail::to($user->email)->send(new VerifyUserMail($user, $code));

                    $user->update(['verified_code' => 1111 /*mt_rand(1111, 9999)*/]);
                    $msg =  $user->verified_code;

                } else {

                    $user->update(['verified_code' => 1111 /*mt_rand(1111, 9999)*/]);
                    $msg =  $user->verified_code;
                    // send_sms($user->phone, $msg);
                }

                return response()->json(['status' => 'success', 'message' => trans('app.auth.sent_code_successfully'), 'data' => null, 'dev_message' => $user->verified_code], 200);

            } else {

                // $verification_type = setting('verification_type');
                $verification_type = 'phone';

                if ($verification_type && $verification_type == 'email') {

                    // $user->notify(new VerifyApiMail(['mail']));

                    // $code = mt_rand(1111, 9999);
                    // Mail::to($user->email)->send(new VerifyUserMail($user, $code));

                    $user->update(['reset_code' => 1111 /*mt_rand(1111, 9999)*/]);
                    $msg =  $user->reset_code;

                } else {

                    $user->update(['reset_code' => 1111 /*mt_rand(1111, 9999)*/]);
                    $msg =  $user->reset_code;
                    // send_sms($user->phone, $msg);
                }

                // $code = 1111;//mt_rand(1111, 9999);
                // $user->update(['reset_code' =>      $code]);
                // // SMS Code
                // $msg =  $code;


                // send_sms($user->phone, $msg);
                return response()->json(['status' => 'success', 'message' => trans('app.auth.sent_code_successfully'), 'data' => ['code' => $user->reset_code]], 200);

            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('app.messages.something_went_wrong_please_try_again'), 'data' => null], 422);
        }
    }


    /**
     * login
     */
    public function login(LoginRequest $request)
    {
        if (!$token = auth('api')->attempt($this->getCredentials($request))) {
            return response()->json(['status' => 'fail', 'data' => null, 'is_active' => false, 'is_ban' => false, 'message' => trans('app.auth.failed')], 402);
        }

        $user = auth()->guard('api')->user();

        if ($user->is_active == 0 && $user->phone_verified_at == null) {

            // $code = 1111;
            // if (setting('use_sms_service') == 'enable') {
            //     $code = mt_rand(1111, 9999); //generate_unique_code(4,'\\App\\Models\\User','verified_code');
            //     $message = trans('app.auth.verified_code_is', ['code' => $code]);
            //     send_sms($user->phone, $message);
            // }

            // $code = mt_rand(1111, 9999);
            // Mail::to($user->email)->send(new VerifyUserMail($user, $code));

            $code = 1111;//mt_rand(1111, 9999);
            $msg = $code;

            // send_sms($user->phone, $msg);

            $user->update(['verified_code' => $code]);

            auth('api')->logout();

            return response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => trans('app.auth.account_is_not_activated'),
                'is_active' => boolval($user->is_active),
                'is_ban' => boolval($user->is_ban),
                'is_verify' => false,
                'dev_message' => $user->verified_code
            ], 403);
        }
        if (!$user->is_active) {

            // $code = 1111;
            // if (setting('use_sms_service') == 'enable') {
            //     $code = mt_rand(1111, 9999); //generate_unique_code(4,'\\App\\Models\\User','verified_code');
            //     $message = trans('app.auth.verified_code_is', ['code' => $code]);
            //     send_sms($user->phone, $message);
            // }
            // $user->update(['verified_code' => $code]);

            // $code = mt_rand(1111, 9999);
            // Mail::to($user->email)->send(new VerifyUserMail($user, $code));

            auth('api')->logout();

            return response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => trans('app.auth.account_is_not_activated'),
                'is_active' => boolval($user->is_active),
                'is_ban' => boolval($user->is_ban),
                // 'dev_message' => $user->verified_code
            ], 403);

        } elseif ($user->is_ban) {

            auth('api')->logout();

            return response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => trans('app.auth.account_banned_by_admin', ['ban_reason' => $user->ban_reason]),
                'is_active' => boolval($user->is_active),
                'is_ban' => boolval($user->is_ban),
            ], 403);

        }

        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            auth('api')->logout();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.auth.Trying_to_sign_up_for_admin_account')]);
        }

        $user->devices()->firstOrCreate($request->only(['device_token', 'type']));


        if ($request->guest_token) {
            $this->addGuestDataToUserCart(['guest_token' => $request->guest_token]);
            favGuestAndUser(['guest_token' => $request->guest_token]);
        }

        $user->profile()->update(['last_login_at' => now()]);

        Wallet::firstOrCreate(
            ['user_id' =>  $user->id],
            ['user_id' =>  $user->id]
        );

        data_set($user, 'token', $token);

        return (new UserResource($user))->additional(['status' => 'success', 'message' => '']);
    }


    // show profile
    public function index()
    {
        $user = auth()->guard('api')->user();
        $token = \JWTAuth::fromUser($user);
        data_set($user, 'token', $token);
        return (new UserResource($user))->additional(['status' => 'success', 'message' => '']);
    }

    // // Edit Profile
    public function store(EditProfileRequest $request)
    {
        $user = auth()->guard('api')->user();
        \DB::beginTransaction();
        try {
            // $phone = Phone::where(['phone' => $request->phone, 'phone_code' => $request->phone_code, 'added_by_id' => auth('api')->id()])->where('verified_at', '!=', null)->first();
            // $current_phone = User::where(['user_type' => 'client', 'phone' => $request->phone, 'phone_code' => $request->phone_code])->first();
            // if (($phone != null) && ($current_phone != null)) {
            //     return response()->json(['status' => 'fail', 'data' => null, 'message' => trans(('app.auth.phone_incorrect_or_not_verified'))], 422);
            // }
            // if (($phone == null) && ($current_phone == null)) {
            //     return response()->json(['status' => 'fail', 'data' => null, 'message' => trans(('app.auth.phone_number_cannot_be_modified_before_confirmation'))], 422);
            // }
            $profile_data = ['country_id', 'city_id', 'lat', 'lng', 'location_description'];

            $user->update(array_except($request->validated(), $profile_data));

            $user->profile()->updateOrCreate(['user_id' => $user->id], array_only($request->validated(), $profile_data));

            if ($request->device_token) {
                $user->devices()->firstOrCreate($request->only(['device_token', 'type']));
            }
            $token = \JWTAuth::fromUser($user);
            data_set($user, 'token', $token);
            // if ($phone  != null) {
            //     $phone->delete();
            // }
            \DB::commit();
            return (new UserResource($user))->additional(['status' => 'success', 'message' => trans('app.messages.edited_successfully')]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }

    public function logout(LogoutRequest $request)
    {
        if (auth()->guard('api')->check()) {
            $user = auth()->guard('api')->user();
            $device = Device::where(['user_id' => auth('api')->id(), 'device_token' => $request->device_token, 'type' => $request->type])->first();
            if ($device) {
                $device->delete();
            }
            $user->profile()->update(['last_login_at' => null]);
            auth('api')->logout();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.signed_out_successfully')]);
        }
    }


    public function updateLang(UpdateLangRequest $request)
    {
        try {
            $user =  auth()->guard('api')->user();
            $user->update($request->validated());
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.messages.edited_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 401);
        }
    }


    public function isNotification()
    {
        $userProfile =  Profile::where('user_id', auth('api')->id())->firstOrFail();
        if ($userProfile->is_allow_notification == 1) {
            $userProfile->update([
                'is_allow_notification' => 0,
            ]);
            $is_notification = false;
        } else {
            $userProfile->update([
                'is_allow_notification' => 1,
            ]);
            $is_notification = true;
        }
        return response()->json(['data' =>  ['is_allow_notification' => $is_notification], 'status' => 'success', 'message' => trans('app.messages.edited_successfully')]);
    }


    // forget password
    public function forgotPassword(ForgetPasswordRequest $request)
    {
        $user = User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code])->where('user_type','client')->firstOrFail();

        // $user = User::where(function($query) use($request) {

        //     $query->where('email',$request->phone)->orWhere('phone',$request->phone);

        // })->where('user_type','client')->firstOrFail();

        try {
            $code = 1111; //mt_rand(1111, 9999);
            $user->update(['reset_code' => $code]);
            // SMS Code
            $msg = $code;
            // send_sms($user->phone, $msg);
            return response()->json(['status' => 'success', 'message' => trans('app.auth.sent_code_successfully'), 'data' => ['code' => $user->reset_code]], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => trans('app.messages.something_went_wrong_please_try_again'), 'data' => null], 422);
        }
    }

    public function checkCode(CheckCodeRequest $request)
    {
        $user = User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code])->where('user_type','client')->firstOrFail();

        // $user = User::where(function($query) use($request) {

        //     $query->where('email',$request->phone)->orWhere('phone',$request->phone);

        // })->where('user_type','client')->first();

        if (!$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.auth.user_not_found')], 404);
        } elseif (!$user->phone_verified_at && $user->verified_code == $request->code) {
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.code_is_true'), 'is_active' => false]);
        } elseif ($user->phone_verified_at && $user->reset_code == $request->code) {
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.code_is_true'), 'is_active' => true]);
        }
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.first_verify_account'), 'is_active' => true]);
    }


    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code])->where('user_type','client')->firstOrFail();

        // $user = User::where(function($query) use($request) {

        //     $query->where('email',$request->phone)->orWhere('phone',$request->phone);

        // })->where('user_type','client')->firstOrFail();

        $user_data = [];
        if (!$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.auth.phone_not_true_or_account_deactive')], 422);
        } elseif (!$user->phone_verified_at && $user->verified_code == $request->code) {
            $user_data = ['password' => $request->password, 'verified_code' => null, 'is_active' => true, 'phone_verified_at' => now()];
        } elseif ($user->phone_verified_at && $user->reset_code == $request->code) {
            $user_data = ['password' => $request->password, 'reset_code' => null];
        }

        $user->update($user_data);

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.success_change_password')]);
    }

    public function editPhone(EditPhoneRequest $request)
    {
        try {
            $code = 1111;
            // $code = mt_rand(1111, 9999);

            $user_data = [
                'verified_code' => $code,
            ];

            $user = auth()->guard('api')->user();

            $user->update($user_data);
            // send Sms to verify phone number

            $msg = $code;
            // send_sms($user->phone, $msg);

            return response()->json(['status' => 'success', 'data' => null, 'message' => '', 'dev_message' => ['code' => $code, 'phone' => $request->phone]]);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }


    public function checkPhoneCode(CheckPhoneCodeRequest $request)
    {
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.code_true')]);
    }


    public function updatePhone(UpdatePhoneRequest $request)
    {
        $user = auth()->guard('api')->user();

        if ($user->verified_code != $request->code) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.auth.code_not_true')], 422);
        }

        $user->update([
            'phone' => $request->phone,
            'verified_code' => null,
        ]);

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.messages.edited_successfully')]);
    }


    // Edit Password
    public function editPassword(UpdatePasswordRequest $request)
    {
        try {
            $user = auth()->guard('api')->user();

            $user->update(array_only($request->validated(), ['password']));

            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.messages.edited_successfully')]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.not_modified_try_again')], 401);
        }
    }

    public function deleteMyAccount()
    {
        $user = auth()->guard('api')->user();
        $user->delete();
        return response()->json(['data' => null, 'status' => 'success', 'message' => trans('app.messages.deleted_successfully')]);
    }
    protected function getCredentials(Request $request)
    {
        $username = $request->phone;
        $credentials = [];
        switch ($username) {
            case filter_var($username, FILTER_VALIDATE_EMAIL):
                $username = 'email';
                break;
            case is_numeric($username):
                $username = 'phone';
                break;
            default:
                $username = 'email';
                break;
        }
        $credentials[$username] = $request->phone;
        if ($request->password) {
            $credentials['password'] = $request->password;
        }
        return $credentials;
    }


    public function addPhone(AddPhoneRequest $request)
    {
        if (User::where(['phone' => $request->phone, 'phone_code' => $request->phone_code])->where('user_type', 'client')->first()) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans(('app.messages.phone_used_before'))], 422);
        }

        $code = 1111;
        // $code = mt_rand(1111, 9999);
        // if (setting('use_sms_service') == 'enable') {
        //     $code = mt_rand(1111, 9999);
        // }
        $msg = $code;
        // send_sms($request->phone, $msg);
        $phone = Phone::create(['phone' => $request->phone, 'phone_code' => $request->phone_code, 'code' => $code, 'added_by_id' => auth('api')->id()]);
        // $this->sendVerifyCode($phone);
        // send_sms($request->phone, $msg);
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.verified_code_send_successfully')]);
    }

    public function verifyPhone(VerifyCodeRequest $request)
    {
        $phone = Phone::where(['phone' => $request->phone, 'phone_code' => $request->phone_code, 'code' => $request->code, 'added_by_id' => auth('api')->id()])->first();

        if ($phone) {
            $phone->update(['verified_at' => now()]);
            auth()->guard('api')->user()->update(['phone' => $request->phone, 'phone_code' => $request->phone_code]);
            $country  = Country::where(['phone_code' => $request->phone_code])->first();
            if ($country) {

                auth()->guard('api')->user()->profile()->update(['country_id' => $country->id]);
            }
            $phone->delete();
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('app.auth.code_true_update_your_phone_success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.auth.code_not_true')], 422);
    }
}
