<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType;
use Illuminate\Support\Collection;

trait RendersOnMenu
{
    /**
     * Determine whether the entry type is visible on menu.
     *
     * @return bool
     */
    public static function isVisibleOnMenu(): bool
    {
        return true;
    }

    /**
     * @mixin EntryType
     * @param Collection $menu
     */
    public static function renderOnMenu(Collection $menu): void
    {
        if ($route = static::routes()->has('index')) {
            $item = \CbtechLtd\Fastlane\Support\Menu\MenuLink::make(static::routes()->get('index')->url(), static::label()['plural'])
                ->group(static::menuGroup())
                ->icon(static::icon() ?? '')
                ->when(function ($user) {
                    return static::isVisibleOnMenu();

                    // TODO: return $user->can('list', static::model());
                });

            $menu->push($item);
        }
    }

    /**
     * The entry type may be rendered inside a group in the menu.
     *
     * @return string
     */
    protected static function menuGroup(): string
    {
        return '';
    }
}
