<?php

namespace App\Http\Controllers\Api\Dashboard\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Profile\UpdateCompanyProfileRequest;
use App\Http\Requests\Api\Dashboard\Profile\UpdatePasswordRequest;
use App\Http\Requests\Api\Dashboard\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\Dashboard\Admin\AdminResource;
use App\Models\Company;
use Exception;


class ProfileController extends Controller
{

    public function update_company_profile(UpdateCompanyProfileRequest $request)
    {
        try {

            $company = Company::first();

            if($company) {
                $company->update($request->validated());
            } else {
                Company::create($request->validated());
            }
            
            return response()->json(['status' => 'status', 'data' => null, 'messages' => trans('dashboard.profile.profile_data_updated')]);

        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.error.fail')], 422);
        }
    }

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
            return response()->json(['status' => 'status', 'data' => AdminResource::make($user), 'messages' => trans('dashboard.profile.profile_data_updated')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.error.fail')], 422);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->guard('api')->user();

        try {
            $user->update($request->safe()->except('old_password'));
            return response()->json(['status' => 'status', 'data' => AdminResource::make($user), 'messages' => trans('dashboard.profile.password_updated_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.error.fail')], 422);
        }
    }
}
