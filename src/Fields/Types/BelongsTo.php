<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Transformers\BelongsToTransformer;
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
        return Str::singular($this->getRelatedEntryType()::key());
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
            return $model->belongsTo(
                $this->getRelatedEntryType()::model(),
                $this->getRelationshipMethod() . '_id',
                $model->getKeyName(),
                $this->getRelationshipMethod()
            );
        };
    }
}
