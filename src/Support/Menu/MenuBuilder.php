<?php

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\Facades\EntryType as EntryTypeFacade;
use CbtechLtd\Fastlane\Support\Menu\Contracts\Menu;
use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuItem;
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
                MenuLink::make(route('fastlane.cp.account'), __('fastlane::core.account_settings.title'))
                    ->icon('user-cog')
                    ->group(__('fastlane::core.menu.system_group'))
            )
            ->mapToGroups(function (MenuItem $item) {
                return ($item->getGroup() === '')
                    ? ['__top' => $item]
                    : [$item->getGroup() => $item];
            })
            ->flatMap(function (Collection $data, $key) {
                if ($key === '__top') {
                    return $data->all();
                }

                return [MenuGroup::make($key)->setChildren($data->all())];
            })
            ->all();
    }
}
