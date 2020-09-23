<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\ContentBlocks;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Slug;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

abstract class ContentBlock implements Arrayable
{
    protected Collection $fieldsData;
    protected string $destination = 'index';

    abstract public static function key(): string;

    abstract public static function name(): string;

    abstract public function fields(): array;

    public static function make(): self
    {
        return new static;
    }

    public function __construct()
    {
        $this->resolveFields();
    }

    public function forDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    public function field(string $field): Field
    {
        return $this->fieldsData->get($field);
    }

    public function withValues(array $data): self
    {
        if ($data['block'] !== static::key()) {
            throw new \Exception('Incompatible Content Block key');
        }

        $this->fieldsData->each(function (Field $field) use ($data) {
            $field->set(data_get($data, "data.{$field->getAttribute()}"));
        });

        return $this;
    }

    public function toArray(): array
    {
        $fields = ($this->destination === 'index')
            ? $this->fieldsData->map(function (Field $field) {
                return $field->toShallowArray();
            })->toArray()
            : $this->fieldsData->toArray();

        return [
            'key'    => static::key(),
            'name'   => static::name(),
            'fields' => $fields,
        ];
    }

    protected function resolveFields(): void
    {
        $defaultFields = Collection::make([
            Slug::make('ID')->required()->unique(),
        ]);

        $this->fieldsData = $defaultFields->merge($this->fields())
            ->mapWithKeys(function (Field $field) {
                $field->resolve();

                return [
                    $field->getAttribute() => $field,
                ];
            });
    }
}
