<?php

namespace CbtechLtd\Fastlane\Support\ApiResources;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResourceBuilder
{
    private EntryType $entryType;
    private Model $item;
    private Request $request;

    public function __construct(EntryType $entryType, Model $item, Request $request)
    {
        $this->entryType = $entryType;
        $this->item = $item;
        $this->request = $request;
    }

    public function build(): JsonResource
    {
        $resource = $this->entryType->apiResource();

        /** @var EntryResource $item */
        $item = new $resource($this->item);

        $item
            ->withOptions([
                'output' => 'single',
            ])
            ->withResolvedFields($this->entryType->schema()->getFields())
            ->withLinks([
                ResourceLink::make('self', ["fastlane.api.{$this->entryType->identifier()}.single", $this->item]),
                ResourceLink::make('top', ["fastlane.api.{$this->entryType->identifier()}.collection"]),
            ])
            ->withMeta([
                ResourceMeta::make('entry_type', [
                    'singular_name' => $this->entryType->name(),
                    'plural_name'   => $this->entryType->pluralName(),
                    'identifier'    => $this->entryType->identifier(),
                ]),
            ]);

        return new ApiResource($item);
    }
}
