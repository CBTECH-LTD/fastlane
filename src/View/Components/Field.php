<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

class Field extends Component
{
    public bool $required;
    public bool $stacked;
    public array $errors;

    public function __construct(bool $required = false, bool $stacked = true, array $errors = [])
    {
        $this->required = $required;
        $this->stacked = $stacked;
        $this->errors = $errors;
    }

    public function render()
    {
        return view('fastlane::components.field');
    }
}
