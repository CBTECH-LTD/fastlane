<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface SchemaField extends Arrayable
{
    public function getType(): string;

    public function getName(): string;
}
