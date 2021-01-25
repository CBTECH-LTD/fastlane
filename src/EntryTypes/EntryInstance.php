<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EntryInstance
{
    protected string $entryType;
    protected FieldCollection $fields;
    protected Model $model;
    protected array $data;

    public function __construct(string $entryType, FieldCollection $fields, Model $model)
    {
        $this->entryType = $entryType;
        $this->fields = $fields;
        $this->model = $model;
    }

    public function id()
    {
        return $this->entryType::entryRouteKey($this->model);
    }

    public function title(): string
    {
        return $this->entryType::entryTitle($this->model);
    }

    public function type(): EntryTypeContract
    {
        return $this->entryType;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function data(): array
    {
        return $this->processData();
    }

    public function get(string $key)
    {
        return Arr::get($this->processData());
    }

    protected function processData(): array
    {
        return once(function () {
            return $this->fields->flattenFields()->getCollection()
                ->mapWithKeys(function (Field $field) {
                    return [
                        $field->getAttribute() => $field->read($this->model, $this->entryType),
                    ];
                })->all();
        });
    }
}
