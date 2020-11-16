<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Fields\Field;

class ShortText extends Field
{
    protected string $formComponent = \CbtechLtd\Fastlane\View\Components\Form\ShortText::class;

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->setInputType('text');
    }

    /**
     * Set the type of the input field.
     *
     * @param string $inputType
     * @return $this
     */
    public function setInputType(string $inputType): self
    {
        return $this->setConfig('inputType', $inputType);
    }

    /**
     * Get the type of the input field.
     *
     * @return string
     */
    public function getInputType(): string
    {
        return $this->getConfig('inputType');
    }
}
