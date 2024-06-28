<?php

namespace App\Http\Controllers\Api\Provider\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Setting\SettingRequest;
use App\Http\Resources\Api\Provider\Setting\SettingResource;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return (new SettingResource(null))->additional(['status' => 'success', 'message' => '']);
    }

    public function store(SettingRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::updateOrCreate(['key' => trim($key)], ['value' => $value]);
        }

        return (new SettingResource(null))->additional(['status' => 'success', 'message' => trans('provider.update.success')]);
    }
}
