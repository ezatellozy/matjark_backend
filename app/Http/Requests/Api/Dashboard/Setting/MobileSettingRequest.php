<?php

namespace App\Http\Requests\Api\Dashboard\Setting;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Tenant\BrandCountry;

class MobileSettingRequest extends ApiMasterRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $allowed = [
            
            'contact_us' => [
                'email', 'phone_number', 'store_address', 'store_location',
            ]
        ];

        return [
            'mobile_settings' => 'required|array',
            'mobile_settings.*.key' => [
                'required',
                'string',
//                Function to check if the key is allowed for the entered type
                function ($attribute, $value, $fail) use ($allowed) {
                    $index = explode('.', $attribute)[1];
                    $type = $this->input('mobile_settings')[$index]['type'];
                    if (!in_array($value, $allowed[$type])) {
                        $fail('The ' . $value . ' is not allowed for the type ' . $type);
                    }
                }
            ],
            'mobile_settings.*.value' => [
                'required',
//                Function to check if the value is allowed for the entered key
                function ($attribute, $value, $fail) use ($allowed) {
                    $index = explode('.', $attribute)[1];
                    $key = $this->input('mobile_settings')[$index]['key'];
                    switch ($key) {
                        case 'card':
                        case 'cash_on_delivery':
                            if (!in_array((int)$value, [0, 1])) {
                                $fail('The ' . $key . ' value must be in 0 or 1');
                            }
                            break;
                        case 'mobile_logo':
                            $file = $this->file('mobile_settings.mobile_logo.value');
                            if (!$file) {
                                $fail('The ' . $key . ' value must be an image');
                            }
                            //                        Validate the image file for mobile_logo mime type and size
                            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                            $maxSize = 2097152; // 2MB
                            if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                                $fail('The ' . $key . ' value must be an image');
                            }
                            if ($file->getSize() > $maxSize) {
                                $fail('The ' . $key . ' value must be less than 2MB');
                            }
                            break;
                        case 'phone_number':
//                            Array of phones each phone holds array of phone_code , phone
                            $phones = $value;
                            foreach ($phones as $phone) {
                                if (!isset($phone['phone_code']) || !isset($phone['phone'])) {
                                    $fail('The ' . $key . ' value must be an array of phone_code and phone');
                                }
//                                Validate the phone_code exists the brand_countries table
                                if (!BrandCountry::where('phone_code', $phone['phone_code'])->exists()) {
                                    $fail('The phone code must be of and existing country');
                                }
//                                Validate the phone to be numeric
                                if (!is_numeric($phone['phone'])) {
                                    $fail('The phone must be numeric');
                                }
                                $digits = BrandCountry::where('phone_code', $phone['phone_code'])->first()->phone_limit ?? 10;
                                if (strlen($phone['phone']) != $digits) {
                                    $fail('The phone must be of ' . $digits . ' digits');
                                }
                            }
                            break;
                        case 'email':
                            if (is_array($value)) {
                                foreach ($value as $email) {
                                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        $fail('The ' . $key . ' value must be a valid email');
                                    }
                                }
                            }
                            break;
                    }
//                    for type social_media_links
                    if (in_array($key, $allowed['social_media_links'])) {
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            $fail('The ' . $key . ' value must be a valid URL');
                        }
                    }
                }
            ],
            'mobile_settings.*.type' => 'required|string|in:' . implode(',', array_keys($allowed)),
        ];
    }
}
