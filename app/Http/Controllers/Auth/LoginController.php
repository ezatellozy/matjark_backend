<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        $credentials=$request->has('username') ? [$this->username() => $request->username, 'password' => $request->password] : $request->only($this->username(), 'password');
        $credentials['is_active'] = 1;
        return $credentials;
    }

    public function username()
    {
        $username = request()->username;
        switch ($username) {
          case filter_var($username, FILTER_VALIDATE_EMAIL):
              $username = 'email';
            break;
          case is_numeric($username):
                $username = 'phone';
                break;
          default:
               $username = 'email';
            break;
        }
        return $username;
    }

    protected function validateLogin(Request $request)
    {
       $username = $this->username() == 'phone' ? ['username' => 'required|numeric'] : ['username' => 'required|email'];
        $request->validate([
           'password' => 'required|string'
        ]+$username);
    }
    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if ((auth()->user()->user_type == 'superadmin' && !auth()->user()->role()->exists()) || (auth()->user()->role()->exists() && auth()->user()->user_type == 'admin')) {
          $this->redirectTo='dashboard/';
          return $this->redirectTo;
          }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
      $locale=app()->getLocale();
      if (request()->is("$locale/dashboard/*") || request()->is("$locale/dashboard") || request()->is("$locale/dashboard/") || request()->is("dashboard/*") || request()->is("dashboard") || request()->is("dashboard/")){
            return view("dashboard.auth.login");
      }

    }

    //To Confirmation Email
    public function confirm($code)
    {
        if( ! $code)
        {
            return redirect('/')->with('false', trans('dashboard.auth.code_not_match'));
        }else{
          // return redirect()->route('getPassword',$code);
          return view('dashboard.auth.getPassword',compact('code'));

        }
    }

    public function storePassword(Request $request)
    {
        $user = User::where('code',$request->code)->first();

        if ( ! $user){
            return redirect('/')->with('false', trans('dashboard.auth.code_not_true'));
        }
      $request->validate([
          'password'=>'required|confirmed|min:6',
      ]);
      $user->update(['password'=>$request->password,'is_active'=>1,'code' => null ,'email_verified_at'=>now()]);
      auth()->login($user);
      if ($user->role()->exists()) {
        return redirect()->route('dashboard')->withTrue(trans('dashboard.auth.success_activate'));
      }
      abort(404);

    }

    public function logout(Request $request)
    {
        $redirect='login';
        if (auth()->check()) {
          if ((auth()->user()->user_type == 'superadmin' && !auth()->user()->role()->exists()) || (auth()->user()->role()->exists() && auth()->user()->user_type == 'admin')) {
            $redirect='dashboard.login';
         }
            $this->guard()->logout();
            $request->session()->invalidate();
            session()->flash('info', trans('dashboard.auth.logout_msg'));
            return redirect()->route($redirect);
        }
    }
}
