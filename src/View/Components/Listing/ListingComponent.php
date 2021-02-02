<?php

namespace CbtechLtd\Fastlane\View\Components\Listing;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\View\Components\Component;
use Illuminate\Database\Eloquent\Model;

abstract class ListingComponent extends Component
{
    public Field $field;
    public EntryInstance $entry;
    public string $attribute;
    public $value;

    public function __construct(EntryInstance $entry, Field $field, string $attribute)
    {
        $this->field = $field;
        $this->entry = $entry;
        $this->attribute = $attribute;
        $this->value = $this->initiateValue($entry->get($this->attribute));
    }

    protected function initiateValue($value)
    {
        return $value;
    }
}
