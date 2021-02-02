<?php

namespace CbtechLtd\Fastlane\Http\Resources;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use Illuminate\Http\Request;

/**
 * Class EntryResource
 *
 * @property EntryInstance $resource
 * @package CbtechLtd\Fastlane\Http\Resources
 */
class EntryResource extends AbstractResource
{
    public function toArray($request)
    {
        return array_merge([
            '$type' => $this->type($request),
            '$id' => $this->resource->id(),
            '$title' => $this->resource->title(),
            '$links' => $this->links($request),
        ], $this->data($request));
    }

    protected function type(Request $request): string
    {
        return $this->resource->type()::key();
    }

    protected function data(Request $request): array
    {
        return [
            'is_active' => $this->resource->is_active,
        ];
    }

    protected function links(Request $request): array
    {
        return $this->resource->links()->toArray();
    }
}
