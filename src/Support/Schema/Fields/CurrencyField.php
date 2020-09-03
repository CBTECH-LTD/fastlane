<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class CurrencyField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected ?string $currency = null;
    protected ?string $mask = null;
    protected $createRules = 'max:255';
    protected $updateRules = 'max:255';

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->mask = config('fastlane.currency.mask');
        $this->currency = config('fastlane.currency.symbol');
    }

    public function getType(): string
    {
        return 'currency';
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'numeric',
        ];
    }

    public function withCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function withMask(string $mask): self
    {
        $this->mask = $mask;
        return $this;
    }

    /**
     * Set the minimum value in cents.
     *
     * @param int $minCents
     * @return CurrencyField
     */
    public function minValue(int $minCents): self
    {
        return $this->setRule('min', $minCents);
    }

    /**
     * Set the maximum value in cents.
     *
     * @param int $maxCents
     * @return CurrencyField
     */
    public function maxValue(int $maxCents): self
    {
        return $this->setRule('max', $maxCents);
    }

    protected function resolveConfig(EntryInstanceContract $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'currency' => $this->currency,
            'mask'     => $this->mask,
            'min'      => $this->getRuleParams('min'),
            'max'      => $this->getRuleParams('max'),
        ]);
    }
}
