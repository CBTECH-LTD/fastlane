<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

trait Sortable
{
    protected bool $sortable = false;

    public function sortable(): self
    {
        $this->sortable = true;
        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }
}
