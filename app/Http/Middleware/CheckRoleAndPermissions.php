<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleAndPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next,$roleOrPermission)
    {
        $user = Auth::user();
        if ($user->hasRole($roleOrPermission) || $user->hasPermission($roleOrPermission) || $user->user_type == 'supper_admin') {
            return $next($request);
        }

        return response()->json(['status'=>'fail','message' => 'Unauthorized', 'data' => null], 403);
    }
}
