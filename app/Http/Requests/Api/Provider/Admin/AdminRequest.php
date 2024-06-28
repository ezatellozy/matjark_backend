<?php

namespace App\Http\Requests\Api\Provider\Admin;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Country;
use App\Models\User;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AdminRequest extends ApiMasterRequest
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
        $provider = isset($this->provider) ? User::where('user_type', 'provider')->findOrFail($this->provider) : null;

        try {
            $status = isset($this->provider) ? 'nullable' : 'required';
            $country_id = $this->country_id ?? optional($provider->country)->id;

            //$country = Country::where('id', $country_id)->first();

            return [
                'fullname'      => $status . '|string|max:45',
                'avatar'        => $status . '|file',
                'phone_code'    => $status . '|exists:countries,phone_code',
                'phone'      => $status . "|numeric|unique:users,phone,".$this->provider,
                'email'      => $status ."|email|unique:users,email,".$this->provider,
                // 'phone'         => [$status, Rule::unique('users')->ignore($provider)->where(fn ($query) => $query->whereNull('deleted_at'))],
                // 'email'         => [$status, Rule::unique('users')->ignore($provider)->where(fn ($query) => $query->whereNull('deleted_at'))],
                'gender'        => $status . '|in:female,male,else',
                'is_active'     => $status . '|in:0,1',
                'is_ban'        => $status . '|in:0,1',
                'ban_reason'    => 'nullable|in:0,1',
                'country_id'    => $status . '|exists:countries,id',
                'city_id'       => $status . '|exists:cities,id,country_id,' . $country_id,
                'password'      => $status . '|min:6|confirmed'
            ];
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json(['status' => 'fail', 'data' => null, 'message' => trans('provider/api.update.fail')], 422));
        }
    }
}
