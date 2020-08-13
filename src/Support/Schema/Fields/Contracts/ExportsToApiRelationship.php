<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ExportsToApiRelationship
{
    public function toApiRelationship(Model $model, array $options = []);
}
