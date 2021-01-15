<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

class ShortText extends ListingComponent
{
    public function render()
    {
        return view('fastlane::components.listing.short-text', [
            'value' => $this->value,
        ]);
    }
}
