<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Exceptions\UnresolvedException;
use CbtechLtd\Fastlane\Support\Concerns\HandlesHooks;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesRules;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\Makeable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ResolvesField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\Sortable;
use CbtechLtd\Fastlane\Fields\Rules\Unique;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Migratable as MigratableContract;
use CbtechLtd\Fastlane\Contracts\Panelizable as PanelizableContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ReadValue as ReadValueContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Resolvable as ResolvableContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithRules as WithRulesContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithVisibility as WithVisibilityContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WriteValue as SupportModelContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Hooks\OnFillingHook;
use Closure;
use Illuminate\Support\Collection;

abstract class AbstractBaseField implements SchemaField, ResolvableContract, ReadValueContract, WithRulesContract, MigratableContract, SupportModelContract, WithVisibilityContract, PanelizableContract
{
    use HandlesHooks, HandlesRules, Makeable, ResolvesField, Sortable;

    const HOOK_BEFORE_FILLING = 'beforeFilling';
    const HOOK_AFTER_FILLING = 'afterFilling';

    protected array $hooks = [
        self::HOOK_BEFORE_FILLING => [],
        self::HOOK_AFTER_FILLING  => [],
    ];

    protected string $name;
    protected string $description = '';
    protected bool $formVisibleLabel = true;
    protected bool $formStackedLabel = false;
    protected $default = null;
    protected ?string $panel = null;
    protected int $listWidth = 0;
    protected ?string $placeholder;
    protected $writeValueCallback;
    protected $readValueCallback;
    protected EntryInstanceContract $entryInstance;

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

    /**
     * Determine the name of the field to be used as the `name` attribute
     * of HTML input elements.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function withStackedLabel(): self
    {
        $this->formStackedLabel = true;
        return $this;
    }

    public function isLabelStacked(): bool
    {
        return $this->formStackedLabel;
    }

    public function hideLabel(): self
    {
        $this->formVisibleLabel = false;
        return $this;
    }

    public function isLabelVisible(): bool
    {
        return $this->formVisibleLabel;
    }

    /**
     * Read the value of the model loaded in the given
     * entry instance.
     *
     * @param EntryInstanceContract $entryInstance
     * @return FieldValue
     */
    public function readValue(EntryInstanceContract $entryInstance): FieldValue
    {
        if (is_callable($this->readValueCallback)) {
            return call_user_func($this->readValueCallback, $entryInstance);
        }

        return $this->buildFieldValueInstance($this->getName(), $entryInstance->model()->{$this->getName()});
    }

    /**
     * Define a custom callback to be used to read the
     * value of models. The callback signature must adhere
     * the following signature:
     *
     * function (CbtechLtd\Fastlane\Support\Contracts\EntryInstance): CbtechLtd\Fastlane\Support\Schema\Fields\FieldValue
     *
     * @param $callback
     * @return $this
     */
    public function readValueUsing($callback): self
    {
        $this->readValueCallback = $callback;
        return $this;
    }

    /**
     * Build an instance of FieldValue class to represent the field value.
     * If you want to use a custom class extending FieldValue, this is
     * the best place to use your custom class.
     *
     * @param string $fieldName
     * @param        $value
     * @return FieldValue
     */
    protected function buildFieldValueInstance(string $fieldName, $value): FieldValue
    {
        return new FieldValue($fieldName, $value);
    }

    // ============================================================
    // ============================================================
    // ============================================================

    public function withDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function setLabel($label): self
    {
        $this->label = $label;
        return $this;
    }

    public function setDefault($default): self
    {
        $this->default = $default;
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

    public function isShownOnIndex(EntryInstanceContract $entryInstance): bool
    {
        if (is_callable($this->showOnIndex)) {
            return call_user_func($this->showOnIndex, $entryInstance);
        }

        return $this->showOnIndex;
    }

    public function isShownOnCreate(EntryInstanceContract $entryInstance): bool
    {
        if (is_callable($this->hideOnCreate)) {
            return call_user_func($this->hideOnCreate, $entryInstance) === false;
        }

        return $this->hideOnCreate === false;
    }

    public function isShownOnUpdate(EntryInstanceContract $entryInstance): bool
    {
        if (is_callable($this->hideOnUpdate)) {
            return ! call_user_func($this->hideOnUpdate, $entryInstance);
        }

        return ! $this->hideOnUpdate;
    }

    public function writeValue(EntryInstanceContract $entryInstance, $value, array $requestData): void
    {
        $fillingHook = new OnFillingHook($entryInstance, $this, $value);
        $this->executeHooks(static::HOOK_BEFORE_FILLING, $fillingHook);

        if (is_callable($this->writeValueCallback)) {
            call_user_func($this->writeValueCallback, $entryInstance, $value, $requestData);
            return;
        }

        $entryInstance->model()->{$this->getName()} = $value ?? $this->getDefault();
        $this->executeHooks(static::HOOK_AFTER_FILLING, $fillingHook);
    }

    public function writeValueUsing($callback): self
    {
        $this->writeValueCallback = $callback;
        return $this;
    }

    public function toArray()
    {
        return [
            'name'          => $this->getName(),
            'type'          => $this->getType(),
            'label'         => $this->getLabel(),
            'description'   => $this->description,
            'placeholder'   => $this->placeholder ?? $this->getLabel(),
            'default'       => $this->default,
            'required'      => $this->required,
            'sortable'      => $this->isSortable(),
            'label_stacked' => $this->isLabelStacked(),
            'label_visible' => $this->isLabelVisible(),
            'listWidth'     => $this->listWidth,
            'panel'         => $this->panel,
            'config'        => $this->getConfig(),
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

    protected function getDefault()
    {
        return $this->default;
    }

    protected function getConfig(): array
    {
        return $this->getResolvedConfig()->all();
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

    protected function getResolvedConfig(?string $config = null, $default = null)
    {
        if (! $this->resolvedConfig) {
            throw new UnresolvedException;
        }

        if (! $config) {
            return $this->resolvedConfig;
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
