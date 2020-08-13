<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\AccountSettingsMenuBuilder;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Controllers\Controller;

abstract class AbstractAccountsController extends Controller
{
    protected function sidebarMenu(): array
    {
        return FastlaneFacade::getMenuManager()->build(
            app()->make(AccountSettingsMenuBuilder::class)
        );
    }
}
