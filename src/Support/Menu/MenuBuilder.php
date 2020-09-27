<?php

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\Facades\EntryType as EntryTypeFacade;
use CbtechLtd\Fastlane\Support\Menu\Contracts\Menu;
use CbtechLtd\Fastlane\View\Components\MenuLink;
use Illuminate\Support\Collection;

class MenuBuilder implements Menu
{
    public function items(): array
    {
        $menu = Collection::make([
            MenuLink::make(route('fastlane.cp.dashboard'), 'Dashboard')->icon('dashboard'),
        ]);

        // Generate links to entry types.
        foreach (EntryTypeFacade::all() as $entryTypeClass) {
            if (is_a($entryTypeClass, RenderableOnMenu::class, true) && $entryTypeClass::isVisibleOnMenu()) {
                $entryTypeClass::renderOnMenu($menu);
            }
        }

        return $menu
            ->push(
                \CbtechLtd\Fastlane\View\Components\MenuLink::make(route('fastlane.cp.account'), __('fastlane::core.account_settings.title'))
                    ->icon('user-cog')
                    ->group(__('fastlane::core.menu.system_group'))
            )
            ->mapToGroups(function ($item) {
                return ($item->getGroup() === '')
                    ? ['__top' => $item]
                    : [$item->getGroup() => $item];
            })
            ->flatMap(function ($items, $key) {
                if ($key === '__top') {
                    return $items->all();
                }

                return [\CbtechLtd\Fastlane\View\Components\MenuGroup::make($key, $items->all())];
            })
            ->values()
            ->all();
    }
}
