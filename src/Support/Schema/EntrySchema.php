<?php

namespace CbtechLtd\Fastlane\Support\Schema;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Resolvable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithVisibility;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EntrySchema implements Contracts\EntrySchema
{
    private EntryInstanceContract $entryInstance;
    private Collection $cache;

    public function __construct(EntryInstanceContract $entryInstance)
    {
        $this->entryInstance = $entryInstance;
        $this->cache = new Collection;
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
        return Collection::make($this->entryInstance->type()->fields())
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

    private function build(Collection $fields, string $destination): Collection
    {
        return $fields->flatMap(function ($field) use ($destination) {
            if ($field instanceof Resolvable) {
                $field->resolve($this->entryInstance, $destination);

                if ($field instanceof FieldPanel) {
                    return $field->getFields();
                }

                return Arr::wrap($field);
            }

            return null;
        });
    }

    private function buildAll(): Collection
    {
        return $this->build(Collection::make($this->entryInstance->type()->fields()), 'index');
    }

    private function buildIndex(): Collection
    {
        return $this->build(
            Collection::make($this->fromCache('all'))->filter(
                fn($f) => $f instanceof WithVisibility && $f->isShownOnIndex()
            ), 'index'
        );
    }

    private function buildCreate(): Collection
    {
        return $this->build(
            Collection::make($this->fromCache('all'))->filter(
                fn($f) => $f instanceof WithVisibility && $f->isShownOnCreate()
            ), 'form'
        );
    }

    private function buildUpdate(): Collection
    {
        return $this->build(
            Collection::make($this->fromCache('all'))->filter(
                fn($f) => $f instanceof WithVisibility && $f->isShownOnUpdate()
            ), 'form'
        );
    }
}
