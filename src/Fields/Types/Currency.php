<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Transformers\CurrencyTransformer;

class Currency extends Field
{
    protected string $formComponent = 'currency';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'mask'     => config('fastlane.currency.mask'),
            'currency' => config('fastlane.currency.code'),
            'min'      => null,
            'max'      => null,
        ]);

    }

    /**
     * Set the currency.
     *
     * @param string $currency
     * @return $this
     */
    public function withCurrency(string $currency): self
    {
        return $this->setConfig('currency', $currency);
    }

    /**
     * Get the currency of the field.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->getConfig('currency');
    }

    /**
     * Set the input mask.
     *
     * @param string $mask
     * @return $this
     */
    public function withMask(string $mask): self
    {
        return $this->setConfig('mask', $mask);
    }

    /**
     * Get the input mask.
     *
     * @return string
     */
    public function getMask(): string
    {
        return $this->getConfig('mask');
    }

    /**
     * @inheritDoc
     */
    public function transformer(): Transformer
    {
        return new CurrencyTransformer($this->getCurrency());
    }

    /**
     * Set minimum and maximum value.
     *
     * @param int|null $min
     * @param int|null $max
     * @return $this
     */
    public function withRange(?int $min = null, ?int $max = null): self
    {
        return $this->setRuleConfig('min', $min)
            ->setRuleConfig('max', $max)
            ->setConfig('min', $min)
            ->setConfig('max', $max);
    }

    /**
     * @inheritDoc
     */
    protected function getFieldRules(array $data, EntryType $entryType): array
    {
        return [
            $this->getAttribute() => 'numeric',
        ];
    }
}
