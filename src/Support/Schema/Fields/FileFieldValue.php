<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use Illuminate\Support\Collection;

class FileFieldValue extends FieldValue
{
    public function items(): Collection
    {
        return Collection::make($this->value());
    }
}
