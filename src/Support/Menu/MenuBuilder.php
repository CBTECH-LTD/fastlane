<?php

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\Support\Menu\Contracts\Menu;
use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuItem;
use Illuminate\Support\Collection;

class MenuBuilder implements Menu
{
    public function items(): array
    {
        $menu = Collection::make([
            MenuLink::make(route('cp.dashboard'), 'Dashboard')->icon('dashboard'),
        ]);

        // Generate links to entry types.
        FastlaneFacade::entryTypes()->each(function (EntryType $entryType) use ($menu) {
            if (! $entryType instanceof RenderableOnMenu) {
                return;
            }

            $entryType->renderOnMenu($menu);
        });

        return $menu
            ->push(
                MenuLink::make(route('cp.account'), 'Account Settings')
                    ->icon('user-cog')
                    ->group('System')
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

                return [MenuGroup::make($key)->children($data->all())];
            })
            ->all();
    }
}
