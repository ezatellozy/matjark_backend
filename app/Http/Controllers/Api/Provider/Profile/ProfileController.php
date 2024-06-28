<?php

namespace App\Http\Controllers\Api\Provider\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Profile\UpdatePasswordRequest;
use App\Http\Requests\Api\Provider\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\Provider\Admin\AdminResource;
use Exception;


class ProfileController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'status', 'data' => AdminResource::make(auth()->guard('api')->user()), 'messages' => '']);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->guard('api')->user();

        try {
            $user->update($request->safe()->except(['country_id', 'city_id']));
            $user->profile()->updateOrCreate($request->safe()->only(['country_id', 'city_id']));
            return response()->json(['status' => 'status', 'data' => AdminResource::make($user), 'messages' => trans('provider.profile.profile_data_updated')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.error.fail')], 422);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->guard('api')->user();

        try {
            $user->update($request->safe()->except('old_password'));
            return response()->json(['status' => 'status', 'data' => AdminResource::make($user), 'messages' => trans('provider.profile.password_updated_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.error.fail')], 422);
        }
    }
}
