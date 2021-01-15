<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

use Illuminate\Support\Arr;

class Select extends ListingComponent
{
    public function render()
    {
        return view('fastlane::components.listing.select');
    }

    protected function initiateValue($value)
    {
        if ($this->field->isMultiple()) {
            $value = json_decode($value);
        }

        return Arr::wrap($value);
    }
}
