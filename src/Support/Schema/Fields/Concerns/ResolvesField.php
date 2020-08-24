<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use Illuminate\Support\Collection;

trait ResolvesField
{
    protected ?Collection $resolvedConfig = null;

    public function resolve(EntryInstanceContract $entryInstance, string $destination): self
    {
        $this->resolvedConfig = new Collection;
        $this->resolveConfig($entryInstance, $destination);

        return $this;
    }

    protected function resolveConfig(EntryInstanceContract $entryInstance, string $destination): void
    {
        //
    }
}
