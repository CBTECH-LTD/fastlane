<?php

namespace CbtechLtd\Fastlane\EntryTypes\Hooks;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use Illuminate\Database\Eloquent\Model;

class OnSavingHook
{
    protected EntryType $entryType;
    protected Model $model;
    protected array $data;

    public function __construct(EntryType $entryType, Model $model, array $data)
    {
        $this->entryType = $entryType;
        $this->model = $model;
        $this->data = $data;
    }

    public function entryType(): EntryType
    {
        return $this->entryType;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function model(): Model
    {
        return $this->model;
    }
}
