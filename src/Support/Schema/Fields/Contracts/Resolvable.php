<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;

interface Resolvable
{
    public function resolve(EntryInstance $entryInstance, string $destination): self;
}
