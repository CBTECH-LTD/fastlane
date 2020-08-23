<?php

namespace CbtechLtd\Fastlane\EntryTypes\Hooks;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Model;

class BeforeHydratingHook
{
    protected EntryInstance $entryInstance;
    protected array $fields;
    public array $data;

    public function __construct(EntryInstance $entryInstance, array $fields, array $data)
    {
        $this->entryInstance = $entryInstance;
        $this->fields = $fields;
        $this->data = $data;
    }

    public function entryInstance(): EntryInstance
    {
        return $this->entryInstance;
    }

    public function entryType(): EntryType
    {
        return $this->entryInstance->type();
    }

    public function model(): Model
    {
        return $this->entryInstance->model();
    }

    public function fields(): array
    {
        return $this->fields;
    }
}
