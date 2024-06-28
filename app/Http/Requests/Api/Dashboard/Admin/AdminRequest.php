<?php

namespace App\Http\Requests\Api\Dashboard\Admin;

use App\Http\Requests\Api\ApiMasterRequest;
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
        $admin = isset($this->admin) ? User::where('user_type', 'admin')->findOrFail($this->admin) : null;

        try {
            $status = isset($this->admin) ? 'nullable' : 'required';
            $country_id = $this->country_id ?? optional($admin->country)->id;

            return [
                'fullname'      => $status . '|string|max:45',
                'avatar'        => $status . '|file',
                'phone_code'    => $status . '|exists:countries,phone_code',
                'phone'      => $status . "|numeric|unique:users,phone,".$this->admin,
                'email'      => $status ."|email|unique:users,email,".$this->admin,
                // 'phone'         => [$status, Rule::unique('users')->ignore($admin)->where(fn ($query) => $query->whereNull('deleted_at'))],
                // 'email'         => [$status, Rule::unique('users')->ignore($admin)->where(fn ($query) => $query->whereNull('deleted_at'))],
                'gender'        => $status . '|in:female,male,else',
                'is_active'     => $status . '|in:0,1',
                'is_ban'        => $status . '|in:0,1',
                'ban_reason'    => 'nullable|in:0,1',
                'country_id'    => $status . '|exists:countries,id',
                'city_id'       => $status . '|exists:cities,id,country_id,' . $country_id,
                'password'      => $status . '|min:6|confirmed',
                "role_id"         => "nullable|exists:roles,id"

            ];
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json(['status' => 'fail', 'data' => null, 'message' => trans('dashboard/api.update.fail')], 422));
        }
    }
}
