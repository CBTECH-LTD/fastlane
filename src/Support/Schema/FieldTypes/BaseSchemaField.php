<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Exceptions\UnresolvedException;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\HandlesHooks;
use CbtechLtd\Fastlane\Support\Schema\Fields\Constraints\Unique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseSchemaField implements SchemaField
{
    use HandlesHooks;

    /** Parameters: EntryRequest $request, Model $entry, array $fields, array $data */
    const HOOK_BEFORE_HYDRATING = 'beforeHydrating';
    /** Parameters: EntryRequest $request, Model $entry, array $fields, array $data */
    const HOOK_AFTER_HYDRATING = 'afterHydrating';

    protected array $hooks = [
        self::HOOK_BEFORE_HYDRATING => [],
        self::HOOK_AFTER_HYDRATING  => [],
    ];

    protected string $name;
    protected string $label;
    protected string $createRules = '';
    protected string $updateRules = '';
    protected bool $required = false;
    protected ?Unique $unique = null;
    protected bool $showOnIndex = false;
    protected bool $showOnCreate = true;
    protected bool $showOnUpdate = true;
    protected ?string $placeholder;
    protected $default = null;
    protected $readCallback;
    protected $hydrateCallback;

    protected ?Collection $resolvedConfig = null;

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

    public function readValueUsing($callback): self
    {
        $this->readCallback = $callback;
        return $this;
    }

    public function readValue(Model $model): array
    {
        if ($this->readCallback) {
            return call_user_func($this->readCallback, $model);
        }

        return [
            $this->getName() => $model->{$this->getName()},
        ];
    }

    public function resolve(EntryTypeContract $entryType, EntryRequest $request): void
    {
        $this->resolvedConfig = Collection::make();

        $this->resolvedConfig->put('config', $this->resolveConfig($entryType, $request));
        $this->resolvedConfig->put('createRules', $this->resolveCreateRules($entryType, $request));
        $this->resolvedConfig->put('updateRules', $this->resolveUpdateRules($entryType, $request));
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder ?? $this->label;
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
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
        return $this->getResolvedConfig('createRules');
    }

    public function setCreateRules(string $rules): self
    {
        $this->createRules = $rules;
        return $this;
    }

    public function getUpdateRules(): string
    {
        return $this->getResolvedConfig('updateRules');
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

    public function hydrateValue($model, $value, EntryRequest $request): void
    {
        if (is_callable($this->hydrateCallback)) {
            call_user_func($this->hydrateCallback, $model, $value, $request);
            return;
        }

        $model->{$this->getName()} = $value;
    }

    public function hydrateUsing($callback): self
    {
        $this->hydrateCallback = $callback;
        return $this;
    }

    public function toArray()
    {
        return [
            'name'        => $this->getName(),
            'type'        => $this->getType(),
            'label'       => $this->getLabel(),
            'placeholder' => $this->getPlaceholder(),
            'default'     => $this->getDefault(),
            'required'    => $this->isRequired(),
            'config'      => array_merge([
                'listWidth' => $this->getListWidth(),
            ], $this->getConfig()),
        ];
    }

    public function toMigration(): string
    {
        $str = [$this->buildMigrationMethodString()];

        if (! $this->isRequired()) {
            $str[] = 'nullable()';
        }

        if ($this->hasUniqueRule()) {
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

    protected function getBaseRules(): string
    {
        $rules = $this->isRequired()
            ? ['required']
            : ['nullable'];

        if ($this->hasUniqueRule()) {
            $rules[] = (string)$this->getUniqueRule();
        }

        return implode('|', $rules);
    }

    protected function getTypeRules(): array
    {
        return [];
    }

    protected function getListWidth(): int
    {
        return 0;
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

    /**
     * @param $str
     * @return string
     */
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

    protected function getResolvedConfig(string $config, $default = null)
    {
        if (! $this->resolvedConfig) {
            throw new UnresolvedException;
        }

        return $this->resolvedConfig->get($config, $default);
    }

    protected function resolveCreateRules(EntryTypeContract $entryType, EntryRequest $request): string
    {
        $baseRules = $this->getBaseRules();

        $rules = array_merge([$baseRules], $this->getTypeRules(), [$this->createRules]);

        return Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|');
    }

    protected function resolveUpdateRules(EntryTypeContract $entryType, EntryRequest $request): string
    {
        $baseRules = $this->getBaseRules();

        $rules = array_merge(['sometimes', $baseRules], $this->getTypeRules(), [$this->updateRules]);

        return Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|');
    }

    protected function resolveConfig(EntryTypeContract $entryType, EntryRequest $request): array
    {
        return [];
    }
}
