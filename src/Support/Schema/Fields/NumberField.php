<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class NumberField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected int $decimals = 0;

    public function getType(): string
    {
        return 'number';
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'numeric',
        ];
    }

    public function withDecimals(int $decimals): self
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * Set the minimum value in cents.
     *
     * @param int $min
     * @return NumberField
     */
    public function min(int $min): self
    {
        return $this->setRule('min', $min);
    }

    /**
     * Set the maximum value in cents.
     *
     * @param int $max
     * @return NumberField
     */
    public function max(int $max): self
    {
        return $this->setRule('max', $max);
    }

    protected function resolveConfig(EntryInstanceContract $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'decimals' => $this->decimals,
            'min' => $this->getRuleParams('min'),
            'max' => $this->getRuleParams('max'),
        ]);
    }
}
