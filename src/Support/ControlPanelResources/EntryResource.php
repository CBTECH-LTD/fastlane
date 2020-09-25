<?php

namespace CbtechLtd\Fastlane\Support\ControlPanelResources;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Http\Request;

class EntryResource extends ResourceType
{
    protected EntryType $entry;
    protected FieldCollection $fields;
    protected FieldCollection $panels;

    public function __construct(EntryType $entry)
    {
        parent::__construct($entry->modelInstance());
        $this->entry = $entry;
    }

    public function type(): string
    {
        return 'fastlane-entry';
    }

    public function id(): string
    {
        return $this->getModel()->getRouteKey() ?? '';
    }

    public function transform(): ApiResource
    {
        return new ApiResource($this);
    }

    public function toListing(): self
    {
        $this->fields = $this->entry->getFields()->onListing();
        $this->panels = FieldCollection::make([]);

        return $this;
    }

    public function toCreate(): self
    {
        $this->fields = $this->entry->getFields()->onCreate();
        $this->panels = $this->entry->getFields()->panels();
        return $this;
    }

    public function toUpdate(): self
    {
        $this->fields = $this->entry->getFields()->onUpdate();
        $this->panels = $this->entry->getFields()->panels();

        return $this;
    }

    public function attributes(Request $request): array
    {
        return $this->getFields()->getData();
    }

    protected function meta(): array
    {
        return [
            ResourceMeta::make('item_label', $this->entry->entryTitle()),
            ResourceMeta::make('entry_type', [
                'schema'        => $this->getFields()->flattenFields()->toArray(),
                'panels'        => $this->getPanels()->toArray(),
                'singular_name' => $this->entry::name(),
                'plural_name'   => $this->entry::pluralName(),
                'identifier'    => $this->entry::key(),
                'icon'          => $this->entry::icon(),
            ]),
        ];
    }

    protected function links(): array
    {
        $links = [];

        if ($this->entry::routes()->has('index')) {
            $links[] = ResourceLink::make('parent', [$this->entry::routes()->get('index')->routeName()]);
        }

        if ($this->model->exists && $this->entry::routes()->has('edit')) {
            $links[] = ResourceLink::make('self', [$this->entry::routes()->get('edit')->routeName(), $this->model]);
        }

        return $links;
    }

    private function getFields()
    {
        return $this->fields;
    }

    private function getPanels()
    {
        return $this->panels;
    }
}
