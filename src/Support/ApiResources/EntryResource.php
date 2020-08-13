<?php

namespace CbtechLtd\Fastlane\Support\ApiResources;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiRelationship;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EntryResource extends ResourceType
{
    protected array $options = [];
    protected array $resolvedFields = [];

    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function type(): string
    {
        return $this->getEntryType()->identifier();
    }

    public function withResolvedFields(array $fields): self
    {
        $this->resolvedFields = $fields;
        return $this;
    }

    public function withOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function attributes(Request $request): array
    {
        return Collection::make($this->resolvedFields)
            ->filter(fn(SchemaField $f) => $f instanceof ExportsToApiAttribute)
            ->mapWithKeys(fn(ExportsToApiAttribute $f) => $f->toApiAttribute($this->model, $this->options))
            ->all();
    }

    protected function relationships(): array
    {
        return Collection::make($this->resolvedFields)
            ->filter(fn(SchemaField $f) => $f instanceof ExportsToApiRelationship)
            ->mapWithKeys(fn(ExportsToApiRelationship $f) => $f->toApiRelationship($this->model, $this->options))
            ->filter()
            ->all();
    }

    protected function meta(): array
    {
        return [];
    }

    protected function links(): array
    {
        return [];
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
}
