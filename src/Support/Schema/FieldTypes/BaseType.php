<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\Constraints\Unique;
use Illuminate\Database\Schema\Blueprint;

abstract class BaseType implements SchemaFieldType
{
    protected string $name;
    protected string $label;
    protected string $createRules = '';
    protected string $updateRules = '';
    protected bool $required = false;
    protected ?Unique $unique = null;
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

    public function hasUniqueRule(): bool
    {
        return $this->unique instanceof Unique;
    }

    public function getUniqueRule(): ?Unique
    {
        return $this->unique;
    }

    public function unique(Unique $unique): self
    {
        $this->unique = $unique;
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
        $baseRules = $this->getBaseRules();

        return "{$baseRules}{$this->getTypeRules()}|{$this->createRules}";
    }

    public function setCreateRules(string $rules): self
    {
        $this->createRules = $rules;
        return $this;
    }

    public function getUpdateRules(): string
    {
        $baseRules = $this->getBaseRules();

        return "sometimes|{$baseRules}{$this->getTypeRules()}|{$this->updateRules}";
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

    public function runOnMigration(Blueprint $table): void
    {
        //
    }

    protected function getBaseRules(): string
    {
        $requiredRule = $this->isRequired()
            ? 'required'
            : 'nullable';

        $uniqueRule = $this->hasUniqueRule()
            ? (string)$this->getUniqueRule() . '|'
            : '';

        return $requiredRule . '|' . $uniqueRule;
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
