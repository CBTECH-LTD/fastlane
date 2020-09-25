<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Auth;

use CbtechLtd\Fastlane\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    public function redirectPath()
    {
        return route('fastlane.cp.dashboard');
    }

    protected function guard()
    {
        return Auth::guard('fastlane-cp');
    }
}
