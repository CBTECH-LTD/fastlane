<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

class Email extends ShortText
{
    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->setInputType('email');
    }

    protected function getFieldRules(array $data): array
    {
        return [
            $this->getAttribute() => 'email',
        ];
    }
}
