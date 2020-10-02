<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\Support\Schema\Fields\Constraints\Unique;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Stringable;

abstract class Field implements Arrayable, Stringable
{
    protected string $component;
    protected string $label;
    protected string $attribute;
    protected Collection $config;
    protected $value = null;

    public static function make(...$attributes): self
    {
        return new static(...$attributes);
    }

    public function __construct(string $label, ?string $attribute = null)
    {
        $this->config = new Collection;
        $this->label = $label;
        $this->attribute = $attribute ?? Str::slug($this->label, '_');
    }

    public function resolve(): void
    {
        $this->resolveConfig();
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * Set whether the field is required.
     *
     * @param bool $required
     * @return $this
     */
    public function required(bool $required = true): self
    {
        $this->config->put('required', $required);
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
        $unique = $rule instanceof Unique
            ? $rule
            : true;

        return $this->setConfig('unique', $unique);
    }

    public function isUnique(): bool
    {
        return $this->getConfig('unique', false);
    }

    public function get()
    {
        return $this->value;
    }

    public function set($value): self
    {
        $this->value = $value;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->getConfig('required', false);
    }

    public function isSortable(): bool
    {
        return $this->getConfig('sortable', false);
    }

    public function getDefault()
    {
        return $this->getConfig('default');
    }

    public function withDefault($default): self
    {
        return $this->setConfig('default', $default);
    }

    public function getPanel(): ?string
    {
        return $this->getConfig('panel', null);
    }

    public function toShallowArray()
    {
        return [
            'value'     => $this->get(),
            'component' => $this->component,
            'name'      => $this->attribute,
            'label'     => $this->label,
            'required'  => $this->isRequired(),
            'sortable'  => $this->isSortable(),
            'panel'     => $this->getPanel(),
            'default'   => $this->getDefault(),
            'config'    => $this->config->except('options')->toArray(),
            // TODO: Remove. It's here only to comply to current FormField implementation on admin frontend.
            'type'      => $this->component,
        ];
    }

    public function toArray()
    {
        return [
            'value'     => $this->get(),
            'component' => $this->component,
            'name'      => $this->attribute,
            'label'     => $this->label,
            'required'  => $this->isRequired(),
            'sortable'  => $this->isSortable(),
            'panel'     => $this->getPanel(),
            'default'   => $this->getDefault(),
            'config'    => $this->config->toArray(),
            // TODO: Remove. It's here only to comply to current FormField implementation on admin frontend.
            'type'      => $this->component,
        ];
    }

    public function __toString()
    {
        return $this->get();
    }

    protected function resolveConfig(): void
    {
        //
    }

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
}
