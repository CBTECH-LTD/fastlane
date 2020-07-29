<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\EntryTypes\Concerns\Resolvable;
use CbtechLtd\Fastlane\Exceptions\UnresolvedException;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Concerns\HandlesHooks;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\Makeable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Constraints\Unique;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Migratable as MigratableContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Panelizable as PanelizableContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Resolvable as ResolvableContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\SupportModel as SupportModelContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithRules as WithRulesContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithValue as WithValueContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithVisibility as WithVisibilityContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseSchemaField implements SchemaField, ResolvableContract, WithValueContract, WithRulesContract, MigratableContract, SupportModelContract, WithVisibilityContract, PanelizableContract
{
    use HandlesHooks, Makeable, Resolvable;

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
    protected ?string $panel = null;
    protected int $listWidth = 0;
    protected $resolveValueCallback;
    protected $fillValueCallback;

    protected ?Collection $resolvedConfig = null;

    protected function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function resolveValueUsing($callback): self
    {
        $this->resolveValueCallback = $callback;
        return $this;
    }

    public function resolveValue(Model $model): array
    {
        if ($this->resolveValueCallback) {
            return call_user_func($this->resolveValueCallback, $model);
        }

        return [
            $this->getName() => $model->{$this->getName()},
        ];
    }

    public function resolve(EntryTypeContract $entryType, EntryRequest $request): array
    {
        $this->resolvedConfig = Collection::make();

        $this->resolvedConfig->put('config', $this->resolveConfig($entryType, $request));
        $this->resolvedConfig->put('createRules', $this->resolveCreateRules($entryType, $request));
        $this->resolvedConfig->put('updateRules', $this->resolveUpdateRules($entryType, $request));

        return [$this];
    }

    public function setPlaceholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
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

    public function fillModel($model, $value, EntryRequest $request): void
    {
        if (is_callable($this->fillValueCallback)) {
            call_user_func($this->fillValueCallback, $model, $value, $request);
            return;
        }

        $model->{$this->getName()} = $value;
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
            'label'       => $this->label,
            'placeholder' => $this->placeholder ?? $this->label,
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

    protected function resolveCreateRules(EntryTypeContract $entryType, EntryRequest $request): array
    {
        $baseRules = $this->getBaseRules();

        $rules = array_merge([$baseRules], $this->getTypeRules(), [$this->createRules]);

        return [
            $this->getName() => Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|'),
        ];
    }

    protected function resolveUpdateRules(EntryTypeContract $entryType, EntryRequest $request): array
    {
        $baseRules = $this->getBaseRules();

        $rules = array_merge(['sometimes', $baseRules], $this->getTypeRules(), [$this->updateRules]);

        return [
            $this->getName() => Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|'),
        ];
    }

    protected function resolveConfig(EntryTypeContract $entryType, EntryRequest $request): array
    {
        return [];
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
}
