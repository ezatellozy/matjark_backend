<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\{LogoutRequest, LoginRequest, SendRequest, CheckRequest, ChangeRequest, SignUpRequest, CodeApiRequest, DriverRegisterSecondStepRequest};
use App\Notifications\Auth\{VerifyApiMail, ResetPassword};
use App\Notifications\General\{GeneralNotification};
use App\Models\{User, Device, Package, GeneralInviteCode};
use App\Http\Resources\Api\User\{UserProfileResource};
use App\Services\{WaslElmService};
use DB;

class AuthController extends Controller
{
    use WaslElmService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup', 'driverRegisterSecondStep', 'confirm', 'sendCode', 'checkCode', 'resetPassword']]);
    }
    // SignUp
    public function driverRegisterSecondStep(DriverRegisterSecondStepRequest $request)
    {
        $driver = User::where(['user_type' => 'driver', 'register_complete_step' => 1])->findOrFail($request->user_id);

        $car_date = ['brand_id', 'car_model_id', 'car_type_id', 'car_licence_image', 'car_form_image', 'car_front_image', 'car_back_image', 'car_insurance_image', 'license_serial_number', 'plate_number', 'plate_letter_right', 'plate_letter_middle', 'plate_letter_left', 'plate_numbers_only', 'manufacture_year', 'plate_type', 'car_color'];

        $driver_type = @$driver->country->is_for_goods_only ? 'delivery' : 'all';

        $driver->car()->create(array_only($request->validated(), $car_date));

        $driver->driver()->create(['is_available' => 1, 'driver_type' => $driver_type]);
        $driver->update(['register_complete_step' => 2]);

        $this->sendVerifyCode($driver);

        return response()->json([
            'status' => 'success', 'data' => null, 'message' => trans('api.messages.success_sign_up'),
            // 'dev_message' => $driver->code
        ]);
    }

    public function signup(SignUpRequest $request)
    {
        // DB::beginTransaction();
        try {
            $profile_date = ['country_id', 'city_id', 'lat', 'lng', 'location_description'];
            $code = 1111;
            if (setting('use_sms_service') == 'enable') {
                $code = mt_rand(1111, 9999); //generate_unique_code(4,'\\App\\Models\\User','verified_code');
            }
            $user_data = [
                'verified_code' => $code,
                'is_active' => 0,
                'user_type' => 'client',
                'referral_code' => generate_unique_code(8, '\\App\\Models\\User', 'referral_code', 'alpha_numbers', 'lower')
            ];

            $user = User::create(array_except($request->validated(), $profile_date) + $user_data);

            $user->profile()->create(array_only($request->validated(), $profile_date) + ['added_by_id' => auth('api')->id()]);


            $this->sendVerifyCode($user);
            return response()->json([
                'status' => 'success', 'data' => null, 'message' => trans('api.messages.success_sign_up'),
                // 'dev_message' => $code
            ]);

            // DB::commit();
        } catch (\Exception $e) {
            // DB::rollback();
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => "لم يتم التسجيل حاول مرة أخرى"], 422);
        }
    }



    /**
     * login
     */
    public function login(LoginRequest $request)
    {
        if (!$token = auth('api')->attempt($this->getCredentials($request))) {
            return response()->json(['status' => 'fail', 'data' => null, 'is_active' => false, 'is_ban' => false, 'message' => trans('api.auth.failed')], 402);
        }

        $user = auth()->guard('api')->user();

        if ($user->user_type == 'driver' && $user->register_complete_step == 1) {
            auth('api')->logout();

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => trans('api.messages.plz_complete_register'),
                'is_complete_register' => false,
                'user_type' => 'driver',
                'user_id' => $user->id,
            ]);
        } elseif (!$user->is_active) {
            $code = 1111;
            if (setting('use_sms_service') == 'enable') {
                $code = mt_rand(1111, 9999); //generate_unique_code(4,'\\App\\Models\\User','verified_code');
                $message = trans('api.auth.verified_code_is', ['code' => $code]);
                // send_sms($user->phone, $message);
            }
            $user->update(['verified_code' => $code]);
            auth('api')->logout();

            return response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => "هذا الحساب غير مفعل ",
                'is_active' => false,
                'is_ban' => false,
                //   'dev_message' => $user->verified_code
            ], 403);
        } elseif ($user->is_ban) {
            auth('api')->logout();
            return response()->json([
                'status' => 'fail',
                'data' => null,
                'message' => "هذا الحساب تم حظرة من قبل الادارة لـ " . $user->ban_reason,
                'is_active' => true,
                'is_ban' => true
            ], 403);
        }
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            auth('api')->logout();
            return response()->json(['status' => 'fail', 'data' => null, 'message' => "محاولة تسجيل بحساب ادمن"]);
        }
        if (!$user->referral_code) {
            $user->update(['referral_code' => generate_unique_code(8, '\\App\\Models\\User', 'referral_code', 'alpha_numbers', 'lower')]);
        }
        $user->devices()->firstOrCreate($request->only(['device_token', 'type']));
        if ($user->user_type == 'driver') {
            $user->driver()->updateOrCreate(['user_id' => $user->id], ['lat' => $request->lat, 'lng' => $request->lng, 'location' => $request->location, 'type' => $request->type, 'device_token' => $request->device_token]);
            // auth('api')->logoutOtherDevices($request->password);
            // $user->devices()->where('device_token',"<>",$request->device_token)->delete();
        }
        $user->profile()->update(['last_login_at' => now()]);
        data_set($user, 'token', $token);
        return (new UserProfileResource($user))->additional(['status' => 'success', 'message' => '']);
    }


    //To Confirmation Email
    public function confirm(CodeApiRequest $request)
    {
        $user = User::where(['verified_code' => $request->code, 'phone' => $request->phone])->whereNull('phone_verified_at')->first();
        if (!$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => "الكود غير صحيح"], 404);
        }
        $user->update(['is_active' => 1, 'verified_code' => null, 'phone_verified_at' => now()]);
        $user->devices()->firstOrCreate($request->only(['device_token', 'type']));
        $token = auth('api')->login($user);


        data_set($user, 'token', $token);
        return (new UserProfileResource($user))->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
            return response()->json(['status' => 'success', 'data' => null, 'message' => "تم تسجيل الخروج بنجاح"]);
        }
    }

    // Forget Password
    public function sendCode(SendRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => "رقم الجوال غير صحيح"]);
        }

        try {
            if ($user->phone_verified_at || $user->email_verified_at) {
                $code = 1111;
                if (setting('use_sms_service') == 'enable') {
                    $code = mt_rand(1111, 9999); //generate_unique_code(4,'\\App\\Models\\User','reset_code');
                    $message = trans('api.auth.reset_code_is', ['code' => $code]);
                    // $response = send_sms($user->phone, $message);
                }
                $user->update(['reset_code' => $code]);
                return response()->json([
                    'status' => 'success', 'data' => null, 'message' => "تم الارسال بنجاح", 'is_active' => true,
                    //   'dev_message' => $code
                ]);
            } else {
                $code = 1111;
                if (setting('use_sms_service') == 'enable') {
                    $code = mt_rand(1111, 9999); //generate_unique_code(4,'\\App\\Models\\User','verified_code');
                    $message = trans('api.auth.verified_code_is', ['code' => $code]);
                    // $response = send_sms($user->phone, $message);
                }
                $user->update(['verified_code' => $code, 'is_active' => 0]);

                return response()->json([
                    'status' => 'success', 'data' => null, 'message' => "تم الارسال بنجاح", 'is_active' => false,
                    //   'dev_message' => $code
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => "لم يتم الارسال"]);
        }
    }

    public function checkCode(CheckRequest $request)
    {
        $user = User::where(['phone' => $request->phone])->first();
        if (!$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('api.auth.user_not_found')], 404);
        } elseif (!$user->phone_verified_at && $user->verified_code == $request->code) {
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('api.auth.code_is_true'), 'is_active' => false]);
        } elseif ($user->phone_verified_at && $user->reset_code == $request->code) {
            return response()->json(['status' => 'success', 'data' => null, 'message' => trans('api.auth.code_is_true'), 'is_active' => true]);
        }
        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('api.auth.first_verify_account'), 'is_active' => true]);
    }

    public function resetPassword(ChangeRequest $request)
    {
        $user = User::where(['phone' => $request->phone])->first();

        $user_data = [];
        if (!$user) {
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('api.auth.phone_not_true_or_account_deactive')], 422);
        } elseif (!$user->phone_verified_at && $user->verified_code == $request->code) {
            $user_data += ['password' => $request->password, 'verified_code' => null, 'is_active' => true, 'phone_verified_at' => now()];
        } elseif ($user->phone_verified_at && $user->reset_code == $request->code) {
            $user_data += ['password' => $request->password, 'reset_code' => null];
        }
        $user->update($user_data);

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('api.auth.success_change_password')]);
    }

    protected function sendVerifyCode($user)
    {
        if (setting('use_sms_service') == 'enable') {
            $message = trans('api.auth.verified_code_is', ['code' => $code]);
            // $response = send_sms($user->phone, $message);

            if (setting('sms_provider') == 'hisms' && $response['response'] != 3) {
                $user->forceDelete();
                $sms_response = $response['result'];
                return response()->json(['status' => 'fail', 'data' => null, 'message' => "لم يتم حفظ رجاء التحقق من البيانات ( " . $sms_response . " )"], 422);
            } else {
                if ($user->user_type == 'driver') {
                    $admin_data = [
                        'title' => ['dashboard.messages.new_register_driver_title'],
                        'body' => ['dashboard.messages.new_register_driver_body', ['driver' => $user->fullname]],
                        'route' => route('dashboard.driver.show', $user->id),
                        'driver_id' => $user->id,
                        'notify_type' => 'new_register',
                    ];
                    $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
                    \Notification::send($admins, new GeneralNotification($admin_data));
                }
            }
        }
    }

    protected function getCredentials(Request $request)
    {
        $username = $request->username;
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
        $credentials[$username] = $request->username;
        $credentials['password'] = $request->password;
        // $credentials['is_active'] = 1;
        return $credentials;
    }
}