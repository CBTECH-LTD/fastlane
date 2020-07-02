<?php

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

trait QueriesActive
{
    public function onlyActive(): self
    {
        $this->where('is_active', true);
        return $this;
    }

    public function exceptActive(): self
    {
        $this->where('is_active', false);
        return $this;
    }
}
