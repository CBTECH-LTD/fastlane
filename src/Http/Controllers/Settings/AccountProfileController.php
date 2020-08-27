<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AccountProfileController extends AbstractAccountsController
{
    public function edit()
    {
        $instance = $this->entryType()->newInstance(Auth::user());

        return $this->render('Settings/ProfileEdit', [
            'item'        => (new EntryResource($instance))->toUpdate()->transform(),
            'links'       => [
                'form' => route('cp.account.profile'),
            ],
            'sidebarMenu' => $this->sidebarMenu(),
        ]);
    }

    public function update(Request $request)
    {
        $this->entryType()->updateAuthenticatedUser($request);

        app('fastlane')->flashSuccess(
            'Your account profile was updated successfully.',
            'thumbs-up'
        );

        return Redirect::back();
    }

    protected function entryType(): BackendUserEntryType
    {
        return app('fastlane')->getEntryTypeByClass(BackendUserEntryType::class);
    }
}
