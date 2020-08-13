<?php

namespace CbtechLtd\Fastlane\Http\Controllers\Account;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Requests\AccountTokensRequest;
use CbtechLtd\Fastlane\Support\ControlPanelResources\TokenResource;
use CbtechLtd\Fastlane\Support\ControlPanelResources\TokenResourceCollection;
use Illuminate\Support\Facades\URL;

class AccountTokensController extends AbstractAccountsController
{
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

    public function create(AccountTokensRequest $request)
    {
        return $this->render('Settings/TokensCreate', [
            'abilities'   => FastlaneFacade::getAccessTokenAbilities(),
            'sidebarMenu' => $this->sidebarMenu(),
            'links'       => [
                'form' => URL::relative('cp.account.tokens.store'),
                'top'  => URL::relative('cp.account.tokens.index'),
            ],
        ]);
    }

    public function store(AccountTokensRequest $request)
    {
        /** @var BackendUserEntryType $entryType */
        $entryType = FastlaneFacade::getEntryTypeByClass(BackendUserEntryType::class);
        $data = $request->validated();

        $token = $entryType->createToken($request->user()->hashid, $data['name'], $data['abilities']);

        return redirect()
            ->route('cp.account.tokens.index')
            ->with('fastlane.account.tokens.newAccessToken', $token->toArray());
    }
}
