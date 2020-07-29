<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use Illuminate\Database\Eloquent\Model;

interface WithValue
{
    public function resolveValue(Model $model): array;

    public function resolveValueUsing($callback): self;
}
