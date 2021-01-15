<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Support\SelectOption;
use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use CbtechLtd\Fastlane\Fields\Transformers\BelongsToTransformer;
use CbtechLtd\Fastlane\Fields\UndefinedValue;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BelongsTo extends Relationship
{
    public function transformer(): Transformer
    {
        return new BelongsToTransformer($this);
    }

    /**
     * @inheritDoc
     */
    public function getRelationshipMethod(): string
    {
        if ($attr = $this->getAttribute()) {
            return Str::camel($attr);
        }

        return Str::camel(Str::singular($this->getRelatedEntryType()::key()), '_');
    }

    public function getRelationshipColumn(): string
    {
        return $this->getRelationshipMethod() . '_id';
    }

    /**
     * @inheritDoc
     */
    public function getRelationshipLabel(): string
    {
        return $this->getRelatedEntryType()::name();
    }

    /**
     * @inheritDoc
     */
    public function getRelationshipResolver(): Closure
    {
        return function (Model $model) {
            if (method_exists($model, $this->getRelationshipMethod())) {
                return $model->{$this->getRelationshipMethod()}();
            }

            return $model->belongsTo(
                $this->getRelatedEntryType()::model(),
                $this->getRelationshipColumn(),
                $model->getKeyName(),
                $this->getRelationshipMethod()
            );
        };
    }

    protected function processWriteValue($value, ?EntryType $entryType = null)
    {
        $method = $this->getRelationshipMethod();

        $entryType->modelInstance()->{$method}()->associate($value);

        return new UndefinedValue;
    }

    protected function processReadValue(Model $model, $value, string $entryType)
    {
        return SelectOptionCollection::make([
            SelectOption::make(
                (string)$value->getKey(),
                $value->name,
            ),
        ]);
    }
}
