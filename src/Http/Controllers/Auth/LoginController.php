<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use CbtechLtd\Fastlane\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:cp')->except('logout');
    }

    public function showLoginForm()
    {
        return $this->render('Auth/Login', [
            'url' => route('cp.login'),
        ]);
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), [
            'is_active' => true,
        ]);
    }


    protected function loggedOut(Request $request)
    {
        return Redirect::route('cp.login');
    }
}
