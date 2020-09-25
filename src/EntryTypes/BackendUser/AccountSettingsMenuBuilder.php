<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\Support\Menu\Contracts\Menu;
use CbtechLtd\Fastlane\Support\Menu\MenuLink;

class AccountSettingsMenuBuilder implements Menu
{
    public function items(): array
    {
        return [
            MenuLink::make(route('fastlane.cp.account.profile'), __('fastlane::core.account_settings.profile_menu')),
            MenuLink::make(route('fastlane.cp.account.security'), __('fastlane::core.account_settings.security_menu')),
            MenuLink::make(route('fastlane.cp.account.tokens.index'), __('fastlane::core.account_settings.personal_access_tokens_menu')),
        ];
    }
}
