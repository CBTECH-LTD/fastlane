<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Exceptions\NotAModelInstanceException;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Fields\Support\SelectOption;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BelongsToField extends RelationField
{
    protected $default = null;
    protected bool $multiple = false;
    protected bool $withTimestamps = false;

    public function readValue(EntryInstanceContract $entryInstance): FieldValue
    {
        /** @var Model $instance */
        $instance = $entryInstance->model()->{$this->getRelationshipName()};
        $relatedModel = $this->getRelatedEntryType()->model();

        if (! is_null($instance) && ! $instance instanceof $relatedModel) {
            throw new NotAModelInstanceException($relatedModel);
        }

        $value = (! is_null($instance))
            ? [
                SelectOption::make(
                    $instance->getKey(),
                    (new EntryInstance($this->getRelatedEntryType(), $instance))->title()
                )->toArray(),
            ] : [];

        return new FieldValue($this->getName(), $value);
    }

    public function isMultiple(): bool
    {
        return false;
    }

    public function getRelationshipLabel(): string
    {
        return $this->getRelatedEntryType()->name();
    }

    public function getRelationshipName(): string
    {
        return Str::singular($this->getRelatedEntryType()->identifier());
    }

    public function getRelationshipMethod(): Closure
    {
        return function (Model $model) {
            return $model->belongsTo(
                $this->getRelatedEntryType()->model(),
                $this->getRelationshipName() . '_id',
                'id'
            );
        };
    }

    protected function hydrateRelation(EntryInstanceContract $entryInstance, $value, array $requestData): void
    {
        $entryInstance->model()->{$this->getRelationshipName()}()->associate($value);
    }
}
