<?php

namespace App\Http\Controllers\Api\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Auth\LoginRequest;
use App\Http\Resources\Api\Dashboard\Admin\AdminResource;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request): Response
    {
        $token = auth('api')->attempt($request->validated());

        if (!$token) return $this->sendResponse('credentials_not_found', null, 'fail', 403);

        $user = auth()->guard('api')->user();

        if (!$user->is_active) {
            auth('api')->logout();
            return $this->sendResponse('not_active', AdminResource::make($user), 'fail', 403);
        }

        if ($user->is_ban) {
            auth('api')->logout();
            return $this->sendResponse('is_ban', AdminResource::make($user), 'fail', 403);
        }

        data_set($user, 'token', $token);
        return $this->sendResponse('login_success', AdminResource::make($user));
    }

    public function logout(): Response
    {
        auth('api')->logout();
        return $this->sendResponse('logout_success');
    }

    public function sendResponse($message, $data = null, $status = 'success', $code = 200)
    {
        return response()->json(['status' => $status, 'data' => $data, 'messages' => trans('dashboard.auth.'.$message)], $code);
    }
}
