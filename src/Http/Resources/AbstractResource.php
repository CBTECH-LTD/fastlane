<?php

namespace CbtechLtd\Fastlane\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class AbstractResource extends JsonResource
{
    /**
     * A string representing the type of the resource.
     *
     * @param Request $request
     * @return string
     */
    abstract protected function type(Request $request): string;

    /**
     * The attributes of the resource.
     *
     * @param Request $request
     * @return array
     */
    abstract protected function data(Request $request): array;

    abstract protected function links(Request $request): array;

    public function toArray($request)
    {
        return array_merge([
            '$type' => $this->type($request),
            '$id' => $this->resource->getKey(),
            '$links' => $this->links($request),
        ], $this->data($request));
    }
}
