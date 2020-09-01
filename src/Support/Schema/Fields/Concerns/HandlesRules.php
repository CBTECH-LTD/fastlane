<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use CbtechLtd\Fastlane\Support\Schema\Fields\Constraints\Unique;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithRules;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Trait ResolvesRules
 *
 * @mixin WithRules
 * @package CbtechLtd\Fastlane\Support\Schema\Fields\Concerns
 */
trait HandlesRules
{
    protected array $rules = [];

    /** @var string | callable */
    protected $createRules = '';

    /** @var string | callable */
    protected $updateRules = '';

    /** @var bool */
    protected bool $required = false;

    /** @var Unique|null */
    protected ?Unique $unique = null;

    /**
     * Determine whether the field is required.
     *
     * @param bool $required
     * @return $this
     */
    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Set the uniqueness of the field.
     *
     * @param Unique $unique
     * @return $this
     */
    public function unique(Unique $unique): self
    {
        $this->unique = $unique;
        return $this;
    }

    /**
     * Define rules to be used both on creation and update
     * of a resource.
     *
     * @param $rules
     * @return $this
     */
    public function withRules($rules): self
    {
        return $this->withCreateRules($rules)->withUpdateRules($rules);
    }

    /**
     * Define rules to be used when creating a resource.
     *
     * @param $rules
     * @return $this
     */
    public function withCreateRules($rules): self
    {
        $this->createRules = $rules;
        return $this;
    }

    /**
     * Build and retrieve the list of rules ready to use
     * for creation.
     *
     * @return array
     */
    public function getCreateRules(): array
    {
        return $this->buildCreateRules();
    }

    /**
     * Define rules to be used when updating a resource.
     *
     * @param $rules
     * @return $this
     */
    public function withUpdateRules($rules): self
    {
        $this->updateRules = $rules;
        return $this;
    }

    /**
     * Build and retrieve the list of rules ready to use
     * for update.
     *
     * @return array
     */
    public function getUpdateRules(): array
    {
        return $this->buildUpdateRules();
    }

    /**
     *  Determine rules required by the field class.
     *
     * @return array
     */
    protected function getTypeRules(): array
    {
        return [];
    }

    /**
     * Construct the base rules used on all operations.
     *
     * @return string
     */
    protected function getBaseRules(): string
    {
        $rules = $this->required
            ? ['required']
            : ['nullable'];

        if ($this->unique instanceof Unique) {
            $rules[] = (string)$this->unique;
        }

        return implode('|', $rules);
    }

    /**
     * Build the rules for creation.
     *
     * @return array
     */
    protected function buildCreateRules(): array
    {
        $rules = array_merge(
            [$this->getBaseRules()],
            [Arr::get($this->getTypeRules(), $this->getName(), '')],
            [$this->createRules]
        );

        return array_merge(Arr::except($this->getTypeRules(), $this->getName()), [
            $this->getName() => Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|'),
        ]);
    }

    /**
     * Build the rules for update.
     *
     * @return array
     */
    protected function buildUpdateRules(): array
    {
        $rules = array_merge(
            ['sometimes', $this->getBaseRules()],
            [Arr::get($this->getTypeRules(), $this->getName(), '')],
            [$this->updateRules]
        );

        return array_merge(Arr::except($this->getTypeRules(), $this->getName()), [
            $this->getName() => Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|'),
        ]);
    }

    protected function setRule(string $rule, string $params = ''): self
    {
        Arr::set($this->rules, $rule, $params);

        return $this;
    }

    protected function getRule(string $name): string
    {
        if (Arr::has($this->rules, $name)) {
            $params = $this->getRuleParams($name, null);

            return $name . ($params ? ":{$params}" : '');
        }

        return '';
    }

    protected function getRuleParams(string $name, $default = null): ?string
    {
        return Arr::get($this->rules, $name, $default);
    }
}
