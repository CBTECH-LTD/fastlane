<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use Closure;
use Illuminate\Database\Eloquent\Model;

class MorphToField extends RelationField
{
    public function isMultiple(): bool
    {
        return false;
    }

    public function getRelationshipName(): string
    {
        return $this->getRelatedEntryType()->identifier();
    }

    public function getRelationshipMethod(): Closure
    {
        return function (Model $model) {
            return $model->morphTo($this->getRelatedEntryType()->model(), 'attachable');
        };
    }

    protected function hydrateRelation(EntryInstanceContract $entryInstance, $value, array $requestData): void
    {
        $entryInstance->type()
            ->addHook(EntryType::HOOK_AFTER_SAVING, function (OnSavingHook $hook, Closure $next) use ($value) {
                $hook->model()->{$this->getRelationshipName()}()->save($value);
            });
    }
}
