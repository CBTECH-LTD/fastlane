<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Exceptions\UnresolvedException;
use CbtechLtd\Fastlane\Support\Concerns\HandlesHooks;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\Makeable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\Resolvable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Constraints\Unique;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Migratable as MigratableContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Panelizable as PanelizableContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Resolvable as ResolvableContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\SupportModel as SupportModelContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithRules as WithRulesContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithValue as WithValueContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithVisibility as WithVisibilityContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Hooks\OnFillingHook;
use Closure;
use Illuminate\Support\Collection;

abstract class AbstractBaseField implements SchemaField, ResolvableContract, WithValueContract, WithRulesContract, MigratableContract, SupportModelContract, WithVisibilityContract, PanelizableContract
{
    use HandlesHooks, Makeable, Resolvable;

    const HOOK_BEFORE_FILLING = 'beforeFilling';
    const HOOK_AFTER_FILLING = 'afterFilling';

    protected array $hooks = [
        self::HOOK_BEFORE_FILLING => [],
        self::HOOK_AFTER_FILLING  => [],
    ];

    protected string $name;
    protected string $createRules = '';
    protected string $updateRules = '';
    protected bool $required = false;
    protected ?Unique $unique = null;
    protected $default = null;
    protected ?string $panel = null;
    protected int $listWidth = 0;
    protected EntryType $entryType;
    protected ?string $placeholder;
    protected $fillValueCallback;

    /** @var string | Closure */
    protected $label;

    /** @var bool | Closure */
    protected $showOnIndex = false;

    /** @var bool | Closure */
    protected $hideOnCreate = false;

    /** @var bool | Closure */
    protected $hideOnUpdate = false;

    protected function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function setEntryType(EntryType $entryType): self
    {
        $this->entryType = $entryType;
        return $this;
    }

    public function setLabel($label): self
    {
        $this->label = $label;
        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    public function unique(Unique $unique): self
    {
        $this->unique = $unique;
        return $this;
    }

    public function setDefault($default): self
    {
        $this->default = $default;
        return $this;
    }

    public function setRules(string $rules): self
    {
        $this->createRules = $rules;
        $this->updateRules = $rules;
        return $this;
    }

    public function getCreateRules(): array
    {
        return $this->getResolvedConfig('createRules');
    }

    public function setCreateRules(string $rules): self
    {
        $this->createRules = $rules;
        return $this;
    }

    public function getUpdateRules(): array
    {
        return $this->getResolvedConfig('updateRules');
    }

    public function setUpdateRules(string $rules): self
    {
        $this->updateRules = $rules;
        return $this;
    }

    /**
     * @param bool | Closure $value
     * @return $this
     */
    public function showOnIndex($value = true): self
    {
        $this->showOnIndex = $value;
        return $this;
    }

    /**
     * @param bool | Closure $value
     * @return $this
     */
    public function hideOnCreate($value = true): self
    {
        $this->hideOnCreate = $value;
        return $this;
    }

    /**
     * @param bool | Closure $value
     * @return $this
     */
    public function hideOnUpdate($value = true): self
    {
        $this->hideOnUpdate = $value;
        return $this;
    }

    /**
     * @param bool | Closure $value
     * @return $this
     */
    public function hideOnForm($value = true): self
    {
        $this->hideOnCreate = $this->hideOnUpdate = $value;
        return $this;
    }

    public function isShownOnIndex(): bool
    {
        if (is_callable($this->showOnIndex)) {
            return call_user_func($this->showOnIndex);
        }

        return $this->showOnIndex;
    }

    public function isShownOnCreate(): bool
    {
        if (is_callable($this->hideOnCreate)) {
            return ! call_user_func($this->hideOnCreate);
        }

        return ! $this->hideOnCreate;
    }

    public function isShownOnUpdate(): bool
    {
        if (is_callable($this->hideOnUpdate)) {
            return ! call_user_func($this->hideOnUpdate);
        }

        return ! $this->hideOnUpdate;
    }

    public function fillModel($model, $value, array $requestData): void
    {
        $beforeHook = new OnFillingHook($this, $model, $value);
        $this->executeHooks(static::HOOK_BEFORE_FILLING, $beforeHook);

        if (is_callable($this->fillValueCallback)) {
            call_user_func($this->fillValueCallback, $model, $value, $requestData);
            return;
        }

        $model->{$this->getName()} = $value;

        $afterHook = new OnFillingHook($this, $model, $value);
        $this->executeHooks(static::HOOK_AFTER_FILLING, $afterHook);
    }

    public function fillModelUsing($callback): self
    {
        $this->fillValueCallback = $callback;
        return $this;
    }

    public function toArray()
    {
        return [
            'name'        => $this->getName(),
            'type'        => $this->getType(),
            'label'       => $this->getLabel(),
            'placeholder' => $this->placeholder ?? $this->getLabel(),
            'default'     => $this->default,
            'required'    => $this->required,
            'listWidth'   => $this->listWidth,
            'panel'       => $this->panel,
            'config'      => $this->getConfig(),
        ];
    }

    public function toMigration(): string
    {
        $str = [$this->buildMigrationMethodString()];

        if (! $this->required) {
            $str[] = 'nullable()';
        }

        if ($this->unique instanceof Unique) {
            $str[] = 'unique()';
        }

        if (! is_null($this->default)) {
            $str[] = 'default(' . $this->escapeString($this->default) . ')';
        }

        return implode('->', $str);
    }

    public function toModelAttribute(): array
    {
        return [$this->getName() => null];
    }

    public function inPanel(FieldPanel $panel): self
    {
        $this->panel = $panel->getName();
        return $this;
    }

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

    protected function getTypeRules(): array
    {
        return [];
    }

    protected function getConfig(): array
    {
        return $this->getResolvedConfig('config');
    }

    protected function getMigrationMethod(): array
    {
        return ['string'];
    }

    protected function buildMigrationMethodString(): string
    {
        $method = $this->getMigrationMethod();
        $methodName = array_shift($method);

        $methodParams = empty($method)
            ? ''
            : ', ' . Collection::make($method)->map(function ($str) {
                return $this->escapeString($str);
            })->implode(', ');

        return "{$methodName}('{$this->getName()}'{$methodParams})";
    }

    protected function getResolvedConfig(string $config, $default = null)
    {
        if (! $this->resolvedConfig) {
            throw new UnresolvedException;
        }

        return $this->resolvedConfig->get($config, $default);
    }

    private function escapeString($str): string
    {
        if (is_null($str)) {
            return 'null';
        }

        if ($str === true) {
            return 'true';
        }

        if ($str === false) {
            return 'false';
        }

        return is_string($str)
            ? "'{$str}'"
            : $str;
    }

    /**
     * @return Closure|string
     */
    protected function getLabel()
    {
        if (is_string($this->label)) {
            return $this->label;

        }

        return call_user_func($this->label);
    }
}
