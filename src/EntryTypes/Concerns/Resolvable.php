<?php

namespace CbtechLtd\Fastlane\EntryTypes\Concerns;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema as EntrySchemaContract;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;

trait Resolvable
{
    protected EntrySchemaContract $schema;

    public function resolve(): EntryTypeContract
    {
        return tap(clone $this, function ($instance) {
            $instance->schema = new EntrySchema($this, app('fastlane')->getRequest());
        });
    }

    public function schema(): EntrySchemaContract
    {
        return $this->schema;
    }
}
