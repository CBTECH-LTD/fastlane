<?php

namespace CbtechLtd\Fastlane\EntryTypes\Hooks;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Model;

class BeforeHydratingHook
{
    protected EntryType $entryType;
    protected EntryRequest $request;
    protected Model $model;
    protected array $fields;
    public array $data;

    public function __construct(EntryType $entryType, EntryRequest $request, Model $model, array $fields, array $data)
    {
        $this->entryType = $entryType;
        $this->request = $request;
        $this->model = $model;
        $this->fields = $fields;
        $this->data = $data;
    }

    public function entryType(): EntryType
    {
        return $this->entryType;
    }

    public function request(): EntryRequest
    {
        return $this->request;
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
