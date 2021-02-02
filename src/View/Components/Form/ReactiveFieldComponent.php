<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\View\Components\Form\Traits\FieldComponentTrait;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;

abstract class ReactiveFieldComponent extends ReactiveComponent
{
    use FieldComponentTrait;

    /**
     * These rules are needed because Livewire requires
     * that rules must be defined for public models.
     * It's weird, but otherwise it does not work.
     *
     * @var \string[][]
     */
    protected $rules = [
        'entry.entry_type' => ['required'],
        'entry.entry_id' => ['required'],
        'entry.for' => ['required'],
    ];

    /**
     * Mount the component.
     *
     * @param EntryInstance $entry
     * @param string        $attribute
     */
    public function mount(EntryInstance $entry, string $attribute)
    {
        $this->attribute = $attribute;
        $this->entry = $entry;
        $this->value = $this->initializeValue();
    }

    /**
     * Get the instance of the field.
     *
     * @return Field
     */
    public function getFieldProperty(): Field
    {
        return (new FieldCollection($this->entry->type()::fields()))->find($this->attribute);
    }

    public function updatedValue($value)
    {
        $this->emit('fastlane::fieldUpdated', [
            'attribute' => $this->attribute,
            'type' => get_class($this->field),
            'value' => $value,
        ]);
    }
}
