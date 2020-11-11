<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;

class Numeric extends Field
{
    protected string $formComponent = 'number';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'decimals' => 0,
        ]);
    }

    /**
     * Set how many decimals the value may have.
     *
     * @param int $decimals
     * @return $this
     */
    public function withDecimals(int $decimals): self
    {
        return $this->setConfig('decimals', $decimals);
    }

    /**
     * Retrieve how many decimals the value may have.
     *
     * @return int
     */
    public function getDecimals(): int
    {
        return $this->getConfig('decimals');
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
