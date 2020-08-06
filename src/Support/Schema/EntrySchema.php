<?php

namespace CbtechLtd\Fastlane\Support\Schema;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Resolvable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithVisibility;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EntrySchema implements Contracts\EntrySchema
{
    private EntryTypeContract $entryType;
    private EntryRequest $request;
    private Collection $cache;

    public function __construct(EntryTypeContract $entryType)
    {
        $this->entryType = $entryType;
        $this->cache = new Collection;
    }

    public function resolve(EntryRequest $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fromCache('all')->all();
    }

    public function getIndexFields(): array
    {
        return $this->fromCache('index')->all();
    }

    public function getCreateFields(): array
    {
        return $this->fromCache('create')->all();
    }

    public function getUpdateFields(): array
    {
        return $this->fromCache('update')->all();
    }

    public function getPanels(): array
    {
        return Collection::make($this->entryType->fields())
            ->filter(fn(SchemaField $f) => $f instanceof FieldPanel)
            ->all();
    }

    public function findField(string $name): SchemaField
    {
        return $this->fromCache('all')->first(
            fn(SchemaField $f) => $f->getName() === $name
        );
    }

    private function fromCache(string $cache): Collection
    {
        if (! $this->cache->has($cache)) {
            $method = 'build' . Str::ucfirst($cache);

            $data = $this->{$method}();

            $this->cache->put($cache, $data);
        }

        return $this->cache->get($cache);
    }

    private function build(Collection $fields): Collection
    {
        return $fields->flatMap(function ($field) {
            if ($field instanceof Resolvable) {
                return Arr::wrap($field->resolve($this->entryType, $this->request));
            }

            return null;
        });
    }

    private function buildAll(): Collection
    {
        return $this->build(Collection::make($this->entryType->fields()));
    }

    private function buildIndex(): Collection
    {
        return $this->build(
            Collection::make($this->fromCache('all'))->filter(
                fn($f) => $f instanceof WithVisibility && $f->isShownOnIndex()
            )
        );
    }

    private function buildCreate(): Collection
    {
        return $this->build(
            Collection::make($this->fromCache('all'))->filter(
                fn($f) => $f instanceof WithVisibility && $f->isShownOnCreate()
            )
        );
    }

    private function buildUpdate(): Collection
    {
        return $this->build(
            Collection::make($this->fromCache('all'))->filter(
                fn($f) => $f instanceof WithVisibility && $f->isShownOnUpdate()
            )
        );
    }
}
