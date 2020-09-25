<?php

namespace CbtechLtd\Fastlane\Support\ControlPanelResources;

use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceTypeCollection;

class TokenResourceCollection extends ResourceTypeCollection
{
    public function resourceType(): string
    {
        return TokenResource::class;
    }

    protected function links(): array
    {
        return [
            ResourceLink::make('create', ['fastlane.cp.account.tokens.create']),
        ];
    }
}
