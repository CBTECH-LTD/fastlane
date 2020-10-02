<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use Illuminate\Support\Collection;

class MultipleSelectFieldValue extends FieldValue
{
    public function value()
    {
        return Collection::make($this->value)->map(
            fn($item) => (object)$item
        );
    }
}
