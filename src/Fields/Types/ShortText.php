<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Fields\Field;

class ShortText extends Field
{
    protected string $formComponent = \CbtechLtd\Fastlane\View\Components\Form\ShortText::class;
}
