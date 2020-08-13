<?php

namespace CbtechLtd\Fastlane\EntryTypes\Hooks;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Model;

class BeforeHydratingHook
{
    protected EntryType $entryType;
    protected Model $model;
    protected array $fields;
    public array $data;

    public function __construct(EntryType $entryType, Model $model, array $fields, array $data)
    {
        $this->entryType = $entryType;
        $this->model = $model;
        $this->fields = $fields;
        $this->data = $data;
    }

    public function entryType(): EntryType
    {
        return $this->entryType;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function fields(): array
    {
        return $this->fields;
    }
}
