<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Fields\Field;

class Textarea extends Field
{
    protected string $formComponent = \CbtechLtd\Fastlane\View\Components\Form\Textarea::class;
}
