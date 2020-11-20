<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOptionCollection;
use Illuminate\Support\Arr;

class Select extends Field
{
    protected string $component = 'select';

    public function get()
    {
        return optional($this->value)->toArray();
    }

    public function set($value): Field
    {
        $value = Arr::wrap($value);

        $this->value = $this->getOptions()->resolveLazyLoad()->filter(
            function (SelectOption $opt) use ($value) {
                return $opt->selected(in_array($opt->getValue(), $value))->isSelected();
            })->values();

        if (! $this->isMultiple()) {
            $this->value = $this->value->first();
        }

        return $this;
    }

    public function multiple(bool $state = true): self
    {
        return $this->setConfig('multiple', $state);
    }

    public function isMultiple(): bool
    {
        return $this->getConfig('multiple', false);
    }

    public function getOptions(): SelectOptionCollection
    {
        return $this->getConfig('options', SelectOptionCollection::make());
    }

    /**
     * @param SelectOptionCollection $collection
     * @return $this
     */
    public function withOptions(SelectOptionCollection $collection): self
    {
        return $this->setConfig('options', $collection);
    }
}
