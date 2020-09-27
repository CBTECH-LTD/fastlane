<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Auth;

use CbtechLtd\Fastlane\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('fastlane.guest:fastlane-cp')->except('logout');
    }

    public function showLoginForm()
    {
        return view('fastlane::auth.login', [
            'url' => route('fastlane.cp.login'),
        ]);
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), [
            'is_active' => true,
        ]);
    }

    public function redirectPath()
    {
        return route('fastlane.cp.dashboard');
    }

    protected function loggedOut(Request $request)
    {
        return Redirect::route('fastlane.cp.login');
    }

    protected function guard()
    {
        return Auth::guard('fastlane-cp');
    }
}
