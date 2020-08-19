<?php

namespace CbtechLtd\Fastlane\Support\Concerns;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Menu\MenuLink;
use Illuminate\Support\Collection;

trait RendersOnMenu
{
    public function isVisibleOnMenu(): bool
    {
        return true;
    }

    protected function menuGroup(): string
    {
        return '';
    }

    /**
     * @mixin EntryType
     * @param Collection $menu
     */
    public function renderOnMenu(Collection $menu): void
    {
        $item = MenuLink::make(route("cp.{$this->identifier()}.index"), $this->pluralName())
            ->group($this->menuGroup())
            ->icon($this->icon())
            ->when(function ($user) {
                return $user->can('list', $this->model());
            });

        $menu->push($item);
    }
}
