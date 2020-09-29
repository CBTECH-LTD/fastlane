<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\View\Components\Listing\ReactiveToggle;

class ActiveToggle extends Boolean
{
    protected int $listingColWidth = 80;

    public function __construct(string $label = 'Active', string $attribute = 'is_active')
    {
        parent::__construct($label, $attribute);
    }

    public function listingComponent(): string
    {
        return ReactiveToggle::class;
    }
}
