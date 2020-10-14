<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\Support\Collection;

class Button extends Component
{
    private string $color;
    private bool $submit;
    private string $size;
    private bool $full;
    private string $variant;
    private bool $disabled;
    public bool $loading;
    public string $leftIcon;
    public string $rightIcon;

    public function __construct(string $color = 'brand', string $size = 'lg', string $variant = 'solid', string $leftIcon = '', string $rightIcon = '', bool $submit = false, bool $full = false, bool $loading = false, bool $disabled = false)
    {
        $this->color = $color;
        $this->submit = $submit;
        $this->size = $size;
        $this->full = $full;
        $this->variant = $variant;
        $this->loading = $loading;
        $this->disabled = $disabled;
        $this->leftIcon = $leftIcon;
        $this->rightIcon = $rightIcon;
    }

    public function render()
    {
        return view('fastlane::components.button');
    }

    public function isDisabled(): bool
    {
        return $this->disabled || $this->loading;
    }

    public function buttonTag(): string
    {
        if ($this->attributes && $this->attributes->get('href')) {
            return 'a';
        }

        return 'button';
    }

    public function buildAttributes(): array
    {
        return [
            'class' => $this->buildClasses(),
            'type'  => $this->submit ? 'submit' : 'button',
        ];
    }

    protected function buildClasses(): string
    {
        $classes = Collection::make([
            "relative btn btn-{$this->color}-{$this->variant} btn-{$this->size}",
        ]);

        if ($this->full) {
            $classes->push('w-full');
        }

        if ($this->loading) {
            $classes->push('btn-loading');
        }

        if ($this->isDisabled()) {
            $classes->push('btn-disabled');
        }

        return $classes->implode(' ');
    }
}
