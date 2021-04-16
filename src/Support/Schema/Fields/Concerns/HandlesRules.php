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

    /** @var Unique|boolean|null */
    protected $unique = null;

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
     * Set the uniqueness of the field. The parameter must be
     * null or a custom Unique instance. If null, Fastlane
     * will just use the entry type's model and the field name
     * as the comparison column.
     *
     * @param Unique|null $rule
     * @return $this
     */
    public function unique(?Unique $rule = null): self
    {
        $this->unique = $rule instanceof Unique
            ? $rule
            : true;

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
     * @return array
     */
    protected function getBaseRules(): array
    {
        $rules = $this->required
            ? ['required']
            : ['nullable'];

        if ($this->unique) {
            $rules[] = (string)($this->unique instanceof Unique)
                ? $this->unique
                : new Unique($this->entryInstance->model()->getTable(), $this->getName());
        }

        return $rules;
    }

    /**
     * Build the rules for creation.
     *
     * @return array
     */
    protected function buildCreateRules(): array
    {
        $rules = array_merge(
            $this->getBaseRules(),
            [Arr::get($this->getTypeRules(), $this->getName(), '')],
            Arr::wrap($this->createRules)
        );

        return array_merge(Arr::except($this->getTypeRules(), $this->getName()), [
            $this->getName() => Collection::make($rules)->filter(fn($r) => ! empty($r))->all(),
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
            ['sometimes'],
            $this->getBaseRules(),
            [Arr::get($this->getTypeRules(), $this->getName(), '')],
            Arr::wrap($this->updateRules),
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
