<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function show()
    {
        $widgets = Collection::make(config('fastlane.dashboard_widgets'))->toArray();

        return $this->render('Dashboard', [
            'widgets' => $widgets,
        ]);
    }
}
