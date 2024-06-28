<?php

namespace App\Http\Requests\Api\Provider\Setting;

use App\Http\Requests\Api\ApiMasterRequest;

class SettingRequest extends ApiMasterRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'                => "nullable|email",
            'use_sms_service'      => "nullable|in:enable,disable",
            'sms_provider'         => "nullable|required_if:use_sms_service,enable|in:hisms,net_powers,sms_gateway",
            'sms_username'         => "nullable|required_if:use_sms_service,enable|string|between:3,250",
            'sms_password'         => "nullable|required_with:sms_username",
            'sms_sender_name'      => "nullable|string|between:3,250",
            'project_name'         => "nullable",
            'facebook'             => "nullable|url",
            'twitter'              => "nullable|url",
            'youtube'              => "nullable|url",
            'instagram'            => "nullable|url",
            'whatsapp'             => "nullable|string|max:250",
            'sms_message'          => "nullable|string|max:250",
            'address'              => "nullable|string|max:250",
            'g_play_app'           => "nullable|url",
            'app_store_app'        => "nullable|url",
            'huawei_store_app'     => "nullable|url",
            'map_api'              => "nullable|string",
            'messenger'            => "nullable|string",
            'linkedin'             => "nullable|string",
            'tiktok'               => "nullable|string",
            'pinterest'            => "nullable|string",
            'about_ar'             => "nullable|string",
            'about_en'             => "nullable|string",
            'privacy_en'           => "nullable|string",
            'privacy_ar'           => "nullable|string",
            'terms_en'             => "nullable|string",
            'terms_ar'             => "nullable|string",
            'banuba_token'         => "nullable|string",
            'shipping_price'       => "nullable|numeric",
            'how_to_shop_ar'          => "nullable|string",
            'how_to_shop_en'          => "nullable|string",
            'return_policy_ar'          => "nullable|string",
            'return_policy_en'          => "nullable|string",
            'lat'                  => "nullable|numeric",
            'lng'                  => "nullable|numeric",
            'description_location' => "nullable|string",
            'phone'                => "nullable|string|max:255",
            'ios_link'             => "nullable|string",
            'android_link'         => "nullable|string",
            'website_link'         => "nullable|string",
            'return_policy'        => "nullable|string|min:4|max:10000",
            'minimum_stock'        => "nullable|numeric|min:0",
            'shipping_on'          => "nullable|in:city,distance",
            'shipping_price'       => "nullable|numeric|min:0",
            'vat_percentage'       => "nullable|numeric|min:0",
            'time_of_reminder_in_minute'  => 'nullable|numeric|min:0',
            'product_return_grace_period' => 'nullable',
            'review_rates'         => 'nullable|in:manually,automatically',
            'total_product_for_discount_shipping' => 'nullable|numeric|min:0',
        ];
    }
}
