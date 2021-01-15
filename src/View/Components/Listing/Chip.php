<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

class Chip extends ListingComponent
{
    public function render()
    {
        return view('fastlane::components.listing.chip', [
            'value' => $this->value,
        ]);
    }
}
