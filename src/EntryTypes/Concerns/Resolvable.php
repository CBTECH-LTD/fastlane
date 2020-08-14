<?php

namespace CbtechLtd\Fastlane\EntryTypes\Concerns;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema as EntrySchemaContract;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;

trait Resolvable
{
    protected EntrySchemaContract $schema;

    public function resolve(array $requestData): EntryTypeContract
    {
        return tap(clone $this, function ($instance) use ($requestData) {
            $instance->schema = new EntrySchema($this, $requestData);
        });
    }

    public function schema(): EntrySchemaContract
    {
        return $this->schema;
    }
}
