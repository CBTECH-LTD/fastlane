<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Requests\AccountUpdateRequest;
use CbtechLtd\Fastlane\Http\Transformers\EntryResource;
use Illuminate\Support\Facades\Redirect;

class AccountProfileController extends AbstractAccountsController
{
    public function edit(AccountUpdateRequest $request)
    {
        return $this->render('Settings/ProfileEdit', [
            'item'        => (new EntryResource($request->entryType()))->toUpdate()->transform(),
            'links'       => [
                'form' => route('fastlane.cp.account.profile'),
            ],
            'sidebarMenu' => $this->sidebarMenu(),
        ]);
    }

    public function update(AccountUpdateRequest $request)
    {
        $request->entryType()->update($request->all());

        Fastlane::flashSuccess(
            __('fastlane::core.account_settings.profile_success_msg'),
            'thumbs-up'
        );

        return Redirect::back();
    }
}
