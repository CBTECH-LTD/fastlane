<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use CbtechLtd\Fastlane\Support\Menu\MenuBuilder;

class AppMainArea extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('fastlane::components.app-main-area');
    }
}
