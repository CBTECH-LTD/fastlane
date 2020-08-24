<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use CbtechLtd\JsonApiTransformer\ApiResources\ResourceRelationship;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait ExportsToApiRelationship
{
    protected ?Closure $toApiRelationshipCallback = null;

    public function exportToApiRelationshipUsing(?Closure $callback = null): self
    {
        $this->toApiRelationshipCallback = $callback;
        return $this;
    }

    public function toApiRelationship(Model $model, array $options = [])
    {
        if ($this->toApiRelationshipCallback) {
            return call_user_func($this->toApiRelationshipCallback, $model);
        }

        return Collection::make($this->readValue($model))->mapWithKeys(function ($value, $key) {
            $key = Str::replaceFirst('relations__', '', $key);

            return [
                $key => null
            ];
        });
    }
}
