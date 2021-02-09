<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\ContentBlocks;

use CbtechLtd\Fastlane\Contracts\ContentBlock as ContentBlockContract;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Fields\Types\Slug;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class ContentBlock implements ContentBlockContract, Arrayable
{
    protected Collection $fields;
    protected bool $shallow = false;
    protected array $values = [];
    protected Model $model;

    public static function make(): self
    {
        return new static;
    }

    public function __construct()
    {
        $this->resolveFields();
    }

    public function shallow(bool $value = true): self
    {
        $this->shallow = $value;

        return $this;
    }

    public function field(string $field): Field
    {
        return $this->fields->get($field);
    }

    public function withModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function withValues(array $data): self
    {
        if ($data['block'] !== static::key()) {
            throw new \Exception('Incompatible Content Block key');
        }

        $this->values = $data['data'];

        return $this;
    }

    public function toArray(): array
    {
        return [
            'key' => static::key(),
            'name' => static::name(),
            'fields' => $this->shallow ? [] : $this->fields->getCollection()->map(function (Field $field) {
                return array_merge($field->toArray(), [
                    'value' => '', // $field->read($this->model, $this->values[$field->getAttribute()] ?? null),
                ]);
            }),
        ];
    }

    protected function resolveFields(): void
    {
        $defaultFields = new FieldCollection([
            Slug::make('ID')->required()->unique()->withDefault(Str::uuid()->toString()),
        ]);

        $this->fields = $defaultFields->merge((new FieldCollection($this->fields())))
            ->mapWithKeys(function (Field $field) {
                return [
                    $field->getAttribute() => $field,
                ];
            });
    }
}
