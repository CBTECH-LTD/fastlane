<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

class DashboardController extends Controller
{
    public function show()
    {
        return view('fastlane::dashboard');
    }
}
