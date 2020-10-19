<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\ValueResolver;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class CurrencyTransformer implements Transformer
{
    private Currency $currency;
    private NumberFormatter $numberFormatter;
    private NumberFormatter $symbolFormatter;

    /**
     * CurrencyTransformer constructor.
     *
     * @param string $currency
     */
    public function __construct(string $currency)
    {
        $this->currency = new Currency($currency);

        // Initialize the number formatter
        $this->numberFormatter = new NumberFormatter(
            config('fastlane.currency.locale', config('app.locale')),
            NumberFormatter::DECIMAL
        );

        $this->numberFormatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);

        // Initialize the symbol formatter
        $this->symbolFormatter = new NumberFormatter(
            config('fastlane.currency.locale', config('app.locale')),
            NumberFormatter::CURRENCY_SYMBOL
        );
    }

    public function get(EntryType $entryType, $value)
    {
        return $value;
    }

    public function set(EntryType $entryType, $value)
    {
        return $value;
    }

    public function getSymbol(): string
    {
        return (new IntlMoneyFormatter($this->symbolFormatter, new ISOCurrencies))
            ->format(new Money(0, $this->currency));
    }

    public function format($value): string
    {
        return (new IntlMoneyFormatter($this->numberFormatter, new ISOCurrencies))
            ->format(new Money($value, $this->currency));
    }

    public function fromRequest(EntryType $entryType, $value): ValueResolver
    {
        return new ValueResolver($entryType, $value);
    }
}
