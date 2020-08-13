<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\AccountSettingsMenuBuilder;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AccountSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $page = Arr::last(explode('/', $request->path()));

        return $this->render('Settings/AccountSettings', [
            'page'        => $page,
            'links' => $this->generateLinksFor($page),
            'sidebarMenu' => FastlaneFacade::getMenuManager()->build(
                app()->make(AccountSettingsMenuBuilder::class)
            ),
        ]);
    }

    public function update(Request $request)
    {
        $page = Arr::last(explode('/', $request->path()));

        dd($request->all());
    }
}
