<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Select extends Field
{
    protected string $component = 'select';

//    public function persistOnModel()
//    {
//        if (! $this->value) {
//            return [];
//        }
//
//        return $this->value->map(
//            fn(SelectOption $opt) => $opt->getValue()
//        )->values()->toArray();
//    }

    public function get()
    {
        return optional($this->value)->toArray();
    }

    public function set($value): Field
    {
        $value = Arr::wrap($value);

        $this->value = $this->getConfig('options', Collection::make())->filter(
            function (SelectOption $opt) use ($value) {
                return $opt->selected(in_array($opt->getValue(), $value))->isSelected();
            })->values();

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

    public function withOptions(array $options): self
    {
        return $this->setConfig('options', Collection::make($options));
    }
}
