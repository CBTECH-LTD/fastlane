<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class Slug extends ReactiveFieldComponent
{
    protected string $view = 'fastlane::components.form.slug';

    protected $listeners = [
        'fastlane::fieldUpdated' => 'fieldUpdated',
    ];
}
