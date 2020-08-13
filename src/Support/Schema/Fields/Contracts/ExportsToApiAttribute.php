<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ExportsToApiAttribute
{
    public function toApiAttribute(Model $model, array $options = []);
}
