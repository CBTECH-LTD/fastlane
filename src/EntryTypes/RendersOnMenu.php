<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Menu\MenuLink;
use Illuminate\Support\Collection;

trait RendersOnMenu
{
    public static function isVisibleOnMenu(): bool
    {
        return true;
    }

    protected static function menuGroup(): string
    {
        return '';
    }

    /**
     * @mixin EntryType
     * @param Collection $menu
     */
    public static function renderOnMenu(Collection $menu): void
    {
        if ($route = static::routes()->has('index')) {
            $item = MenuLink::make(static::routes()->get('index')->url(), static::pluralName())
                ->group(static::menuGroup())
                ->icon(static::icon())
                ->when(function ($user) {
                    return $user->can('list', static::model());
                });

            $menu->push($item);
        }
    }
}
