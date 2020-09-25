<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Fields\Support\SelectOption;
use Closure;
use Illuminate\Database\Eloquent\Model;

class BelongsToManyField extends RelationField
{
    protected $default = [];
    protected bool $multiple = true;

    public function readValue(EntryInstanceContract $entryInstance): FieldValue
    {
        $value = $entryInstance->model()->{$this->getRelationshipName()}->map(
            fn(Model $related) => SelectOption::make(
                $related->getKey(),
                (new EntryInstance($this->getRelatedEntryType(), $related))->title(),
            )
        )->toArray();

        return new FieldValue($this->getName(), $value);
    }

    public function getRelationshipLabel(): string
    {
        return $this->relatedEntryType->pluralName();
    }

    public function getRelationshipName(): string
    {
        return $this->relatedEntryType->identifier();
    }

    public function getRelationshipMethod(): Closure
    {
        return function (Model $model) {
            $rel = $model->belongsToMany($this->relatedEntryType->model());

            if ($this->withTimestamps) {
                $rel->withTimestamps();
            }

            return $rel;
        };
    }

    protected function hydrateRelation(EntryInstanceContract $entryInstance, $value, array $requestData): void
    {
        $entryInstance->type()
            ->addHook(EntryType::HOOK_AFTER_SAVING, function (OnSavingHook $hook, Closure $next) use ($value) {
                $hook->model()->{$this->getRelationshipName()}()->sync($value);

                $next($hook);
            });
    }
}
