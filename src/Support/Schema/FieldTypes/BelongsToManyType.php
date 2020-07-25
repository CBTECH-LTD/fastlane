<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use Closure;
use Illuminate\Database\Eloquent\Model;

class BelongsToManyType extends RelationType
{
    protected $default = [];
    protected bool $multiple = true;

    public function isMultiple(): bool
    {
        return true;
    }

    public function getRelationshipName(): string
    {
        return $this->relatedEntryType->identifier();
    }

    public function getRelationshipMethod(): Closure
    {
        return function (Model $model) {
            return $model
                ->belongsToMany($this->relatedEntryType->model())
                ->withTimestamps();
        };
    }

    protected function hydrateRelation($model, $value, EntryRequest $request): void
    {
        $model->{$this->getRelationshipName()}()->sync($value);
    }
}
