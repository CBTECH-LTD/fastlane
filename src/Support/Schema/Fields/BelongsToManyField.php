<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use Closure;
use Illuminate\Database\Eloquent\Model;

class BelongsToManyField extends RelationField
{
    protected $default = [];
    protected bool $multiple = true;

    public function resolveValue(Model $model): array
    {
        $values = $model->{$this->getRelationshipName()}->map(
            fn(Model $related) => SelectOption::make(
                $related->getKey(),
                $this->relatedEntryType->makeModelTitle($related)
            )
        )->toArray();

        return [$this->getName() => $values];
    }

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
            $rel = $model
                ->belongsToMany($this->relatedEntryType->model());

            if ($this->withTimestamps) {
                $rel->withTimestamps();
            }

            return $rel;
        };
    }

    protected function hydrateRelation($model, $value, EntryRequest $request): void
    {
        $request->entryType()->addHook(EntryType::HOOK_AFTER_SAVING, function (OnSavingHook $hook, Closure $next) use ($value) {
            $hook->model()->{$this->getRelationshipName()}()->sync($value);
        });
    }
}
