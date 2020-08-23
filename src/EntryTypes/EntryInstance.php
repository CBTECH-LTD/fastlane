<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Eloquent\Contracts\LoadAttributesFromEntryType;
use CbtechLtd\Fastlane\Support\Eloquent\Contracts\LoadRelationshipsFromEntryType;
use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema as EntrySchemaContract;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ReadValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EntryInstance implements EntryInstanceContract
{
    protected EntryTypeContract $type;
    protected EntrySchemaContract $schema;
    protected Model $model;

    public function __construct(EntryTypeContract $entryType, Model $model)
    {
        $this->type = $entryType;
        $this->model = $model;

        $this->resolve();
    }

    public function id()
    {
        return $this->model->getKey();
    }

    public function title(): string
    {
        if (method_exists($this->model, 'toString')) {
            return $this->model->toString();
        }

        /** @var ReadValue $field */
        $field = Collection::make($this->schema()->getFields())
            ->first(fn(SchemaField $f) => $f instanceof ReadValue);

        $value = ! is_null($field)
            ? $field->readValue($this)->value()
            : null;

        return $value ?? '';
    }

    public function type(): EntryTypeContract
    {
        return $this->type;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function schema(): EntrySchemaContract
    {
        return $this->schema;
    }

    public function saveModel(): self
    {
        if ($this->model()->isDirty()) {
            $this->model()->save();
        }

        return $this;
    }

    public function toArray()
    {
        return [];
    }

    protected function resolve(): void
    {
        $this->schema = new EntrySchema($this);

        if ($this->model instanceof LoadRelationshipsFromEntryType) {
            $this->model->loadRelationsFromEntryType($this);
        }

        if ($this->model instanceof LoadAttributesFromEntryType) {
            $this->model->loadAttributesFromEntryType($this);
        }


    }
}