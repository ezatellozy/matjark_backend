<?php
namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $public_routes = [
            'dashboard.login' ,
            'dashboard.logout' ,
            'dashboard.notification.index' ,
            'dashboard.profile.get_profile',
            'dashboard.profile.update_profile',
            'dashboard.profile.update_password'
        ];

        if (auth()->guard('api')->check() && auth()->guard('api')->user()->user_type == 'superadmin' && auth()->guard('api')->user()->user_type == 'admin')
        {
            return $next($request);
        }
        // elseif (auth()->check() && auth()->user()->role()->exists() && auth()->user()->user_type == 'admin'){
        //     if (auth()->user()->hasPermissions(str_before(str_after($request->route()->getName() , '.') , '.') , $request->route()->getActionMethod()) || in_array($request->route()->getName(), $public_routes)){
        //         return $next($request);
        //     }elseif (auth()->user()->hasPermissions(str_before(str_after($request->route()->getName() , '.') , '.') , 'update') && ($request->route()->getActionMethod() == 'index' || $request->route()->getActionMethod() == 'edit')){
        //         return $next($request);
        //     }elseif (auth()->user()->hasPermissions(str_before(str_after($request->route()->getName() , '.') , '.') , 'destroy') && ($request->route()->getActionMethod() == 'destroy' || $request->route()->getActionMethod() == 'index')){
        //         return $next($request);
        //     }elseif (auth()->user()->hasPermissions(str_before(str_after($request->route()->getName() , '.') , '.') , 'index') && $request->route()->getActionMethod() == 'show'){
        //         return $next($request);
        //     }elseif (auth()->user()->hasPermissions(str_before(str_after($request->route()->getName() , '.') , '.') , 'store') && $request->route()->getActionMethod() == 'create'){
        //         return $next($request);
        //     }elseif ($request->is(app()->getLocale() . "/dashboard/search")){
        //         return $next($request);
        //     }else{
        //         if ($request->ajax() && auth()->user()->hasPermissions(str_before(str_after($request->route()->getName() , '.') , '.') , 'destroy') && ($request->route()->getActionMethod() == 'destroy')){
        //             return $next($request);
        //         }elseif ($request->ajax() && $request->route()->getActionMethod() != 'destroy'){
        //             return $next($request);
        //         }elseif ($request->ajax() && ! auth()->user()->hasPermissions(str_before(str_after($request->route()->getName() , '.') , '.') , 'destroy') && ($request->route()->getActionMethod() == 'destroy')){
        //             return response()->json(['value' => 0 , 'message' => trans('dashboard.error.403_msg')]);
        //         }

        //         return response()->view('dashboard.error.403');
        //     }

        //     return $next($request);
        // }

        // return redirect(route('dashboard.login'))->withFalse(trans('dashboard.messages.login_firstly'));
    }
}
