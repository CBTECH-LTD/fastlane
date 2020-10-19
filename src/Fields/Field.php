<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Rules\Unique;
use CbtechLtd\Fastlane\Fields\Types\Panel;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;

abstract class Field implements Arrayable
{
    protected string $component;
    protected Collection $config;
    protected Collection $visibility;
    protected Collection $rules;
    protected string $arrayFormat = 'listing';

    /** @var mixed */
    protected $value;

    /** @var null|Closure */
    protected ?Closure $writeResolverCallback = null;

    /** @var null|Closure */
    protected ?Closure $readResolverCallback = null;

    /** @var int */
    protected int $listingColWidth = 0;

    public static function make(...$attributes): self
    {
        return new static(...$attributes);
    }

    public function __construct(string $label, ?string $attribute = null)
    {
        $this->config = new Collection([
            'label'     => $label,
            'attribute' => $attribute ?? Str::slug($label, '_'),
            'required'  => false,
            'unique'    => false,
            'sortable'  => false,
            'default'   => null,
            'panel'     => null,
            'computed'  => false,
            'listing'   => new Collection([
                'colWidth' => $this->listingColWidth,
            ]),
        ]);

        $this->visibility = new Collection([
            'listing' => false,
            'create'  => true,
            'update'  => true,
        ]);

        $this->rules = new Collection([
            'config' => Collection::make(),
            'create' => fn() => '',
            'update' => fn() => '',
        ]);
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->config->get('attribute');
    }

    /**
     * Set whether the field is required.
     *
     * @param bool $required
     * @return $this
     */
    public function required(bool $required = true): self
    {
        return $this->setConfig('required', $required);
    }

    /**
     * Determine whether the field is required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->getConfig('required');
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
        $unique = $rule instanceof Unique
            ? $rule
            : true;

        return $this->setConfig('unique', $unique);
    }

    /**
     * Determine whether the field is unique.
     *
     * @return bool
     */
    public function isUnique(): bool
    {
        $value = $this->getConfig('unique');

        return $value instanceof Unique || $value === true;
    }

    /**
     * Set the field as computed. Computed fields are not used
     * to retrieve or write data from/to models.
     *
     * @return $this
     */
    public function computed(): self
    {
        return $this->setConfig('computed', true);
    }

    /**
     * Determine whether a field is computed.
     *
     * @return bool
     */
    public function isComputed(): bool
    {
        return $this->getConfig('computed');
    }

    /**
     * Set the rules used when saving the field.
     *
     * @param string|callable $rules
     * @return $this
     */
    public function withRules($rules): self
    {
        return $this->withCreateRules($rules)->withUpdateRules($rules);
    }

    /**
     * Set the rules used when creating the field.
     *
     * @param callable|string $rules
     * @return $this
     */
    public function withCreateRules($rules): self
    {
        $callback = is_callable($rules)
            ? $rules
            : function (array $data, EntryType $entryType) use ($rules) {
                return $rules;
            };

        $this->rules->put('create', $callback);
        return $this;
    }

    /**
     * Set the rules used when updating the field.
     *
     * @param callable|string $rules
     * @return $this
     */
    public function withUpdateRules($rules): self
    {
        $callback = is_callable($rules)
            ? $rules
            : function (array $data, EntryType $entryType) use ($rules) {
                return $rules;
            };

        $this->rules->put('update', $callback);
        return $this;
    }

    /**
     * Retrieve the rules used when creating an entry.
     *
     * @param array     $data
     * @param EntryType $entryType
     * @return array
     */
    public function getCreateRules(array $data, EntryType $entryType): array
    {
        $customRules = $this->rules->get('create')($data, $entryType);

        $rules = array_merge(
            [$this->buildBaseRules($data, $entryType)],
            [Arr::get($this->getFieldRules($data, $entryType), $this->getAttribute(), '')],
            [$customRules],
        );

        return array_merge(Arr::except($this->getFieldRules($data, $entryType), $this->getAttribute()), [
            $this->getAttribute() => Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|',),
        ]);
    }

    public function getUpdateRules(array $data, EntryType $entryType): array
    {
        $customRules = $this->rules->get('update')($data, $entryType);

        $rules = array_merge(
            ['sometimes', $this->buildBaseRules($data, $entryType)],
            [Arr::get($this->getFieldRules($data, $entryType), $this->getAttribute(), '')],
            [$customRules],
        );

        return array_merge(Arr::except($this->getFieldRules($data, $entryType), $this->getAttribute()), [
            $this->getAttribute() => Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|',),
        ]);
    }

    /**
     * Enable the field to be sorted in listings.
     *
     * @return $this
     */
    public function sortable(): self
    {
        return $this->setConfig('sortable', true);
    }

    /**
     * Determine whether the field is sortable in listings.
     *
     * @return bool
     */
    public function isSortable(): bool
    {
        $value = $this->getConfig('sortable');

        return $value !== null && $value !== false;
    }

    /**
     * Set the default value of the field.
     *
     * @param $default
     * @return $this
     */
    public function withDefault($default): self
    {
        return $this->setConfig('default', $default);
    }

    /**
     * Get the default value of the field.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->getConfig('default');
    }

    /**
     * Set the panel which field will be rendered in.
     *
     * @param Panel $panel
     * @return $this
     */
    public function inPanel(Panel $panel): self
    {
        return $this->setConfig('panel', $panel->getAttribute());
    }

    /**
     * Get the panel where the field is rendered in.
     *
     * @return string|null
     */
    public function getPanel(): ?string
    {
        return $this->getConfig('panel');
    }

    /**
     * Set the field as visible in the listing pages.
     *
     * @return $this
     */
    public function listable(): self
    {
        $this->visibility->put('listing', true);
        return $this;
    }

    /**
     * Check whether the field is visible on listing pages.
     *
     * @return bool
     */
    public function isListable(): bool
    {
        return $this->visibility->get('listing');
    }

    /**
     * Set whether the field should not be present on create form.
     *
     * @return $this
     */
    public function hideOnCreate(): self
    {
        $this->visibility->put('create', false);
        return $this;
    }

    /**
     * Check whether the field is visible on the create form.
     *
     * @return bool
     */
    public function isVisibleOnCreate(): bool
    {
        return $this->visibility->get('create');
    }

    /**
     * Set whether the field should not be present on update form.
     *
     * @return $this
     */
    public function hideOnUpdate(): self
    {
        $this->visibility->put('update', false);
        return $this;
    }

    /**
     * Check whether the field is visible on the update form.
     *
     * @return bool
     */
    public function isVisibleOnUpdate(): bool
    {
        return $this->visibility->get('update');
    }

    /**
     * Set whether the field should not be present on both forms.
     *
     * @return $this
     */
    public function hideOnForm(): self
    {
        return $this->hideOnCreate()->hideOnUpdate();
    }

    public function setArrayFormat(string $format): self
    {
        Assert::inArray($format, ['listing', 'create', 'update']);

        $this->arrayFormat = $format;
        return $this;
    }

    public function read($value, ?EntryType $entryType = null)
    {
        if (is_callable($this->readResolverCallback)) {
            return $this->readResolverCallback->call($this, $value, $entryType);
        }

        return $this->processReadValue($value);
    }

    public function write($value, ?EntryType $entryType = null)
    {
        if (is_callable($this->writeResolverCallback)) {
            return $this->writeResolverCallback->call($this, $value, $entryType);
        }

        return $this->processWriteValue($value);
    }

    /**
     * Set a custom callable to read the value from the model.
     *
     * @param callable $callable
     * @return $this
     */
    public function readUsing(callable $callable): self
    {
        $this->readResolverCallback = $callable;
        return $this;
    }

    /**
     * Set a custom callable to write the value to the model.
     *
     * @param callable $callable
     * @return $this
     */
    public function writeUsing(callable $callable): self
    {
        $this->writeResolverCallback = $callable;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $config = (function () {
            $config = $this->config->except('attribute');

            if ($this->arrayFormat === 'listing') {
                return $this->prepareConfigToListing($config);
            }

            if ($this->arrayFormat === 'create') {
                return $this->prepareConfigToCreate($config);
            }

            if ($this->arrayFormat === 'update') {
                return $this->prepareConfigToUpdate($config);
            }

            throw new \Exception('Invalid arrayFormat value.');
        })();

        return [
            'attribute' => $this->getConfig('attribute'),
            'component' => $this->component,
            'value'     => $this->value,
            'config'    => $config,
        ];
    }

    protected function prepareConfigToListing(Collection $config): array
    {
        return $config->toArray();
    }

    protected function prepareConfigToCreate(Collection $config): array
    {
        return $config->toArray();
    }

    protected function prepareConfigToUpdate(Collection $config): array
    {
        return $config->toArray();
    }

    /**
     * Build the base rules.
     *
     * @param array     $data
     * @param EntryType $entryType
     * @return string
     */
    protected function buildBaseRules(array $data, EntryType $entryType): string
    {
        $rules = $this->isRequired()
            ? ['required']
            : ['nullable'];

        if ($this->isUnique()) {
            $rules[] = (string)($this->getConfig('unique') instanceof Unique)
                ? $this->getConfig('unique')
                : new Unique($entryType->modelInstance()->getTable(), $this->getAttribute());
        }

        $rules = array_merge(
            $rules,
            $this->rules->get('config')->map(function ($rule) use ($data, $entryType) {
                $value = is_callable($rule['params'])
                    ? call_user_func($rule['params'], $data, $entryType)
                    : $rule['params'];

                return "{$rule['rule']}:{$value}";
            })->all()
        );

        return implode('|', $rules);
    }

    /**
     * Get the rules specific for the field.
     *
     * @param array     $data
     * @param EntryType $entryType
     * @return array
     */
    protected function getFieldRules(array $data, EntryType $entryType): array
    {
        return [];
    }

    /**
     * Get the parameters of the given config collection or null
     * if it's not set.
     *
     * @param string $rule
     * @return $this
     */
    protected function getRuleConfig(string $rule): self
    {
        return $this->rules->get('config')->first(
            fn($r) => $r['rule'] === $rule
        );
    }

    /**
     * Add a rule to the config collection.
     *
     * @param string     $rule
     * @param mixed|null $params
     * @return $this
     */
    protected function setRuleConfig(string $rule, $params = ''): self
    {
        if (is_null($params)) {
            return $this->unsetRuleConfig($rule);
        }

        $this->rules->get('config')->push(compact('rule', 'params'));
        return $this;
    }

    /**
     * Remove the given rule from the config collection.
     *
     * @param string $rule
     * @return $this
     */
    protected function unsetRuleConfig(string $rule): self
    {
        $this->rules->put('config', $this->rules->get('config')->filter(
            fn($r) => $r['rule'] !== $rule
        ));

        return $this;
    }

    /**
     * @param string $key
     * @param null   $value
     * @return mixed
     */
    protected function getConfig(string $key, $value = null)
    {
        return $this->config->get($key, $value);
    }

    protected function setConfig(string $key, $value): self
    {
        $this->config->put($key, $value);
        return $this;
    }

    protected function mergeConfig(array $config): self
    {
        $this->config = $this->config->merge($config);
        return $this;
    }

    protected function processReadValue($value)
    {
        return $value;
    }

    protected function processWriteValue($value)
    {
        return $value;
    }
}
