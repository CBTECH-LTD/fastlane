<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AccountSecurityController extends AbstractAccountsController
{
    public function edit()
    {
        return $this->render('Settings/SecurityEdit', [
            'links'       => [
                'form' => route('cp.account.security'),
            ],
            'sidebarMenu' => $this->sidebarMenu(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        Auth::user()->setPasswordAttribute($data['password'])->save();

        app('fastlane')->flashSuccess(
            'Your account password was updated successfully.',
            'thumbs-up'
        );

        return Redirect::route('cp.account.profile');
    }

    protected function entryType(): BackendUserEntryType
    {
        return app('fastlane')->getEntryTypeByClass(BackendUserEntryType::class)
            ->new(Auth::user())
            ->resolve([]);
    }
}
