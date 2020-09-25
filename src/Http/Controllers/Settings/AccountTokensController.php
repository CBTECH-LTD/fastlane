<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Facades\EntryType;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Requests\AccountTokensRequest;
use CbtechLtd\Fastlane\Support\ControlPanelResources\TokenResource;
use CbtechLtd\Fastlane\Support\ControlPanelResources\TokenResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\PersonalAccessToken;

class AccountTokensController extends AbstractAccountsController
{
    /**
     * @param AccountTokensRequest $request
     * @return \Inertia\Response
     */
    public function index(AccountTokensRequest $request)
    {
        $collection = TokenResourceCollection::make(
            $request->user()
                ->tokens()
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn($t) => new TokenResource($t))
                ->all()
        );

        $newAccessToken = session('fastlane.account.tokens.newAccessToken');

        return $this->render('Settings/TokensIndex', [
            'items'          => $collection->transform(),
            'sidebarMenu'    => $this->sidebarMenu(),
            'newAccessToken' => $newAccessToken,
        ]);
    }

    /**
     * Show the form to create a new access token.
     *
     * @param AccountTokensRequest $request
     * @return \Inertia\Response
     */
    public function create(AccountTokensRequest $request)
    {
        return $this->render('Settings/TokensCreate', [
            'abilities'   => Fastlane::getAccessTokenAbilities(),
            'sidebarMenu' => $this->sidebarMenu(),
            'links'       => [
                'form' => URL::relative('fastlane.cp.account.tokens.store'),
                'top'  => URL::relative('fastlane.cp.account.tokens.index'),
            ],
        ]);
    }

    /**
     * Store the new access token.
     *
     * @param AccountTokensRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AccountTokensRequest $request)
    {
        $entryType = $this->getUserEntry();
        $data = $request->validated();

        $token = $entryType->createToken($data['name'], $data['abilities']);

        return redirect()
            ->route(Fastlane::makeCpRouteName('account.tokens.index'))
            ->with('fastlane.account.tokens.newAccessToken', $token->toArray());
    }

    /**
     * Revoke the given access token.
     *
     * @param PersonalAccessToken $token
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(PersonalAccessToken $token)
    {
        $token->delete();

        return redirect()->route(
            Fastlane::makeCpRouteName('account.tokens.index')
        );
    }

    protected function getUserEntry(): BackendUserEntryType
    {
        return EntryType::findByClass(BackendUserEntryType::class)::newInstance(Auth::user());
    }
}
