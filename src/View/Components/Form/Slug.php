<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use Illuminate\Support\Str;

/**
 * Class Slug
 *
 * @property \CbtechLtd\Fastlane\Fields\Types\Slug $field
 * @package CbtechLtd\Fastlane\View\Components\Form
 */
class Slug extends ReactiveFieldComponent
{
    protected string $view = 'fastlane::components.form.slug';

    protected $listeners = [
        'fastlane::fieldUpdated' => 'fieldUpdated',
    ];

    public function fieldUpdated(array $data): void
    {
        if ($data['attribute'] === $this->field->getBaseField() && trim(empty($this->value))) {
            $this->fill([
                'value' => Str::slug($data['value']),
            ]);

            $this->updatedValue(Str::slug($data['value']));
        }
    }
}
