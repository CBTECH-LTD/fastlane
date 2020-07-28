<?php

namespace CbtechLtd\Fastlane\EntryTypes\Concerns;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema as EntrySchemaContract;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;

trait Resolvable
{
    protected EntrySchemaContract $schema;

    public function resolveForRequest(EntryRequest $request): EntryTypeContract
    {
        return tap(clone $this, function ($instance) use ($request) {
            $instance->schema = (new EntrySchema($this))->resolve($request);
        });
    }

    public function schema(): EntrySchemaContract
    {
        return $this->schema;
    }
}
