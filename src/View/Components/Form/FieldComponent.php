<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\View\Components\Component;
use CbtechLtd\Fastlane\View\Components\Form\Traits\FieldComponentTrait;

abstract class FieldComponent extends Component
{
    use FieldComponentTrait;

    public Field $field;

    /**
     * FieldComponent constructor.
     *
     * @param EntryInstance $entry
     * @param Field         $field
     */
    public function __construct(EntryInstance $entry, Field $field)
    {
        $this->entry = $entry;
        $this->field = $field;
        $this->attribute = $field->getAttribute();
        $this->value = $this->initializeValue();
    }

    /**
     * Prepare the data provided to the component view.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [];
    }
}
