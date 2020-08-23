<?php

namespace CbtechLtd\Fastlane\EntryTypes\Hooks;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Model;

class OnSavingHook
{
    protected EntryInstanceContract $entryInstance;
    protected array $data;

    public function __construct(EntryInstanceContract $entryInstance, array $data)
    {
        $this->entryInstance = $entryInstance;
        $this->data = $data;
    }

    public function entryInstance(): EntryInstanceContract
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

    public function data(): array
    {
        return $this->data;
    }
}
