<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

class Boolean extends ListingComponent
{
    public function render()
    {
        return view('fastlane::components.listing.boolean', [
            'value' => $this->value,
        ]);
    }
}
