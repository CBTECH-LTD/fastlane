<?php

namespace CbtechLtd\Fastlane\Support\ApiResources;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Requests\API\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiRelationship;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class EntryResource extends ResourceType
{
    private EntryRequest $request;
    protected array $options = [];

    public function __construct(Model $model, EntryRequest $request)
    {
        parent::__construct($model);
        $this->request = $request;
    }

    public function type(): string
    {
        return $this->getEntryType()->identifier();
    }

    public function withOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function attributes(Request $request): array
    {
        return $this->getSchemaFields()
            ->filter(fn(SchemaField $f) => $f instanceof ExportsToApiAttribute)
            ->mapWithKeys(fn(ExportsToApiAttribute $f) => $f->toApiAttribute($this->model, $this->options))
            ->all();
    }

    protected function relationships(): array
    {
        return $this->getSchemaFields()
            ->filter(fn(SchemaField $f) => $f instanceof ExportsToApiRelationship)
            ->mapWithKeys(fn(ExportsToApiRelationship $f) => $f->toApiRelationship($this->model, $this->options))
            ->filter()
            ->all();
    }

    protected function meta(): array
    {
        return [
            ResourceMeta::make('entry_type', [
                'singular_name' => $this->getEntryType()->name(),
                'plural_name'   => $this->getEntryType()->pluralName(),
                'identifier'    => $this->getEntryType()->identifier(),
            ]),
        ];
    }

    protected function links(): array
    {
        $identifier = $this->getEntryType()->identifier();

        return [
            ResourceLink::make('self', ["fastlane.api.{$identifier}.single", $this->model]),
            ResourceLink::make('top', ["fastlane.api.{$identifier}.collection"]),
        ];
    }

    protected function getEntryType(): EntryType
    {
        return FastlaneFacade::getEntryTypeByClass($this->getEntryTypeClass());
    }

    protected function getEntryTypeClass(): string
    {
        $class = Str::replaceLast(
            'Resource',
            'EntryType',
            (new \ReflectionClass($this))->getName()
        );

        if (! class_exists($class)) {
            throw new \Exception($class . ' does not exist.');
        }

        return $class;
    }

    protected function getSchemaFields(): Collection
    {
        return Collection::make($this->request->entryType()->schema()->getFields());
    }
}
