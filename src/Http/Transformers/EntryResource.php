<?php

namespace CbtechLtd\Fastlane\Http\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EntryResource implements Arrayable
{
    protected EntryType $entry;
    protected string $to;
    protected bool $includeSchema = false;
    protected array $additionalMeta = [];
    protected array $additionalLinks = [];

    public static function toListing(EntryType $entryType): self
    {
        return new static($entryType, 'listing');
    }

    public static function toCreate(EntryType $entryType): self
    {
        return new static($entryType, 'create');
    }

    public static function toUpdate(EntryType $entryType): self
    {
        return new static($entryType, 'update');
    }

    public function __construct(EntryType $entry, string $to)
    {
        $this->entry = $entry;
        $this->to = $to;
    }

    public function withSchema(): self
    {
        $this->includeSchema = true;
        return $this;
    }

    public function model(): Model
    {
        return $this->entry->modelInstance();
    }

    public function id(): string
    {
        return $this->model()->getRouteKey() ?? '';
    }

    public function attributes(): array
    {
        return $this->getFields()->getData();
    }

    private function getFields()
    {
        if ($this->to === 'listing') {
            return $this->entry->getFields()->onListing();
        }

        if ($this->to === 'create') {
            return $this->entry->getFields()->onCreate();
        }

        if ($this->to === 'update') {
            return $this->entry->getFields()->onUpdate();
        }

        return FieldCollection::make();
    }

    private function getPanels()
    {
        if ($this->to === 'listing') {
            return Collection::make();
        }

        return $this->entry->getFields()->panels();
    }

    public function links(): array
    {
        $links = [
            'top' => $this->entry::routes()->has('index') ? $this->entry::routes()->get('index')->url() : null,
        ];

        if ($this->entry->modelInstance()->exists && $this->entry::routes()->has('edit')) {
            $links['self'] = $this->entry::routes()->get('edit')->url($this->entry->entryRouteKey());
        }

        if (! $this->entry->modelInstance()->exists && $this->entry::routes()->has('store')) {
            $links['self'] = $this->entry::routes()->get('store')->url();
        }

        return $links;
    }

    public function meta(): array
    {
        return [
            'item_label' => $this->entry->entryTitle(),
            'entry_type' => [
                'identifier'    => $this->entry::key(),
                'singular_name' => $this->entry::name(),
                'plural_name'   => $this->entry::pluralName(),
                'icon'          => $this->entry::icon(),
                'fields'        => $this->getFields()->toArray(),
                'panels'        => $this->getPanels()->toArray(),
            ],
        ];
    }

    public function toArray()
    {
        return [
            'id'         => $this->entry->entryRouteKey(),
            'attributes' => $this->attributes(),
            'links'      => $this->buildLinks(),
            'meta'       => $this->buildMeta(),
        ];
    }

    protected function buildLinks(): array
    {
        return Collection::make($this->links())
            ->merge($this->additionalLinks)
            ->map(function ($value) {
                return is_callable($value)
                    ? call_user_func($value, $this->entry)
                    : $value;
            })->filter()->toArray();
    }

    protected function buildMeta(): array
    {
        return Collection::make($this->meta())
            ->merge($this->additionalMeta)
            ->map(function ($value) {
                return is_callable($value)
                    ? call_user_func($value, $this->entry)
                    : $value;
            })->filter()->toArray();
    }
}
