<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Hooks;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Database\Eloquent\Model;

class OnFillingHook
{
    private EntryInstance $entryInstance;
    private SchemaField $field;
    private $value;

    public function __construct(EntryInstance $entryInstance, SchemaField $field, $value)
    {
        $this->entryInstance = $entryInstance;
        $this->field = $field;
        $this->value = $value;
    }

    public function field(): SchemaField
    {
        return $this->field;
    }

    public function entryInstance(): EntryInstance
    {
        return $this->entryInstance;
    }

    public function model(): Model
    {
        return $this->entryInstance->model();
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
