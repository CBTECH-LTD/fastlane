<?php

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Menu\Contracts\Menu;

class MenuBuilder implements Menu
{
    /**
     * Determine whether the builder must automatically
     * include links for entry types.
     *
     * @var bool
     */
    protected bool $autoIncludeEntryTypes = true;

    public function items(): array
    {
        $baseLinks = [
            MenuLink::make(route('cp.dashboard'), 'Dashboard')->icon('dashboard'),
        ];

        if (! $this->autoIncludeEntryTypes) {
            return $baseLinks;
        }

        // Generate links to entry types.
        $typeLinks = FastlaneFacade::entryTypes()->map(function (EntryType $entryType) {
            if (! $entryType->isVisibleOnMenu()) {
                return null;
            }

            return MenuLink::make(route("cp.{$entryType->identifier()}.index"), $entryType->pluralName())
                ->icon($entryType->icon())
                ->when(function ($user) use ($entryType) {
                    return $user->can('list', $entryType->model());
                });
        })->filter();

        return array_merge($typeLinks->all(), $baseLinks);
    }
}
