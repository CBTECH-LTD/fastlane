<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\AccountSettingsMenuBuilder;
use CbtechLtd\Fastlane\Http\Controllers\Controller;
use CbtechLtd\Fastlane\Support\Menu\MenuManager;

abstract class AbstractAccountsController extends Controller
{
    protected function sidebarMenu(): array
    {
        return (new MenuManager())->build(
            app()->make(AccountSettingsMenuBuilder::class)
        );
    }
}
