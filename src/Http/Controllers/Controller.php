<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;

class Controller extends BaseController
{
    use AuthorizesRequests;

    public function render(string $view, array $data = [])
    {
        return Inertia::render($view, $data);
    }
}
