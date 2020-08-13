<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\Support\Menu\Contracts\Menu;
use CbtechLtd\Fastlane\Support\Menu\MenuLink;

class AccountSettingsMenuBuilder implements Menu
{
    public function items(): array
    {
        return [
            MenuLink::make(route('cp.account.profile'), 'Profile'),
            MenuLink::make(route('cp.account.security'), 'Security'),
            MenuLink::make(route('cp.account.tokens.index'), 'Personal Access Tokens'),
        ];
    }
}
