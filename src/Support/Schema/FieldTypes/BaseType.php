<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;

abstract class BaseType implements SchemaFieldType
{
    protected string $name;
    protected string $label;
    protected string $createRules = '';
    protected string $updateRules = '';
    protected bool $required = false;
    protected bool $showOnIndex = false;
    protected bool $showOnCreate = true;
    protected bool $showOnUpdate = true;
    protected $default = null;

    protected function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public static function make(string $name, string $label): self
    {
        return new static($name, $label);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    public function setRules(string $rules): self
    {
        $this->createRules = $rules;
        $this->updateRules = $rules;
        return $this;
    }

    public function getCreateRules(): string
    {
        $requiredRule = $this->isRequired()
            ? 'required'
            : 'nullable';

        return "{$requiredRule}|{$this->getTypeRules()}|{$this->createRules}";
    }

    public function setCreateRules(string $rules): self
    {
        $this->createRules = $rules;
        return $this;
    }

    public function getUpdateRules(): string
    {
        $requiredRule = $this->isRequired()
            ? 'required'
            : 'nullable';

        return "sometimes|{$requiredRule}|{$this->getTypeRules()}|{$this->updateRules}";
    }

    public function setUpdateRules(string $rules): self
    {
        $this->updateRules = $rules;
        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setDefault($default): self
    {
        $this->default = $default;
        return $this;
    }

    public function showOnIndex(bool $state = true): self
    {
        $this->showOnIndex = $state;
        return $this;
    }

    public function hideOnCreate(bool $state = true): self
    {
        $this->showOnCreate = ! $state;
        return $this;
    }

    public function hideOnUpdate(bool $state = true): self
    {
        $this->showOnUpdate = ! $state;
        return $this;
    }

    public function hideOnForm(bool $state = true): self
    {
        $this->showOnCreate = $this->showOnUpdate = ! $state;
        return $this;
    }

    public function isShownOnIndex(): bool
    {
        return $this->showOnIndex;
    }

    public function isShownOnCreate(): bool
    {
        return $this->showOnCreate;
    }

    public function isShownOnUpdate(): bool
    {
        return $this->showOnUpdate;
    }

    public function toArray()
    {
        return [
            'name'     => $this->getName(),
            'type'     => $this->getType(),
            'label'    => $this->getLabel(),
            'default'  => $this->getDefault(),
            'required' => $this->isRequired(),
            'config'   => $this->getConfig(),
        ];
    }

    protected function getTypeRules(): string
    {
        return '';
    }

    protected function getConfig(): array
    {
        return [];
    }
}
