<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Requests\AccountUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AccountSecurityController extends AbstractAccountsController
{
    public function edit(AccountUpdateRequest $request)
    {
        return $this->render('Settings/SecurityEdit', [
            'links'       => [
                'form' => route('fastlane.cp.account.security'),
            ],
            'sidebarMenu' => $this->sidebarMenu(),
        ]);
    }

    public function update(AccountUpdateRequest $request)
    {
        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        Auth::user()->setPasswordAttribute($data['password'])->save();

        Fastlane::flashSuccess(
            __('fastlane::core.flash.password_updated'),
            'thumbs-up'
        );

        return Redirect::route('fastlane.cp.account.profile');
    }
}
