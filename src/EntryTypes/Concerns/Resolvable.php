<?php

namespace CbtechLtd\Fastlane\EntryTypes\Concerns;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\LoadsAttributesFromEntryType;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\LoadsRelationsFromEntryType;
use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema as EntrySchemaContract;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;

trait Resolvable
{
    protected EntrySchemaContract $schema;

    public function resolve(array $requestData): EntryTypeContract
    {
        $this->schema = new EntrySchema($this, $requestData);

        // Dynamically load relations and attributes when the underlying
        // model has the required traits.
        $modelInstanceTraits = class_uses_recursive($this->modelInstance);

        if (in_array(LoadsRelationsFromEntryType::class, $modelInstanceTraits)) {
            $this->modelInstance->loadRelationsFromEntryType($this);
        }

        if (in_array(LoadsAttributesFromEntryType::class, $modelInstanceTraits)) {
            $this->modelInstance->loadAttributesFromEntryType($this);
        }

        return $this;
    }

    public function schema(): EntrySchemaContract
    {
        return $this->schema;
    }
}
