<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\View\Components\Component;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

class RowCellRenderer extends Component
{
    public EntryInstance $entry;
    public Field $field;

    public function __construct(EntryInstance $entry, Field $field)
    {
        $this->field = $field;
        $this->entry = $entry;
    }

    public function renderedComponent()
    {
        $component = $this->field->listingComponent();

        $props = ' :field="$field" :entry="$entry" attribute="' . $this->field->getAttribute() . '" :value="$getValue()"';

        return is_a($component, ReactiveComponent::class, true)
            ? '<livewire:' . $component::tag() . $props . '/>'
            : '<x-' . $component::tag() . $props . '/>';
    }

    public function render()
    {
        return Blade::compileString($this->renderedComponent());
    }

    public function getValue()
    {
        return $this->entry->get($this->field->getAttribute());
    }
}
