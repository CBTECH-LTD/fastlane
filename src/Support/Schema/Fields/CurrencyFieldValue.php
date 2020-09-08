<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class CurrencyFieldValue extends FieldValue
{
    private Money $money;
    private IntlMoneyFormatter $formatter;

    public function __construct(string $name, $value)
    {
        parent::__construct($name, $value);

        $this->money = new Money($value, new Currency(config('fastlane.currency.code')));

        $numberFormatter = new \NumberFormatter(
            config('fastlane.currency.locale', config('app.locale')),
            \NumberFormatter::DECIMAL
        );

        $numberFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 0);

        $this->formatter = new IntlMoneyFormatter(
            $numberFormatter,
            new ISOCurrencies,
        );
    }

    public function asMoney(): string
    {
        return config('fastlane.currency.symbol') . ' ' . $this->formatter->format($this->money);
    }
}
