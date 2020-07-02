<?php

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuItem;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class MenuGroup implements MenuItem
{
    private string $label;
    private ?string $icon = null;
    private ?\Closure $whenFn = null;
    private array $children = [];

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public static function make(string $label): MenuGroup
    {
        return new static($label);
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function children(array $children): self
    {
        $interface = MenuItem::class;
        Assert::allImplementsInterface($children, $interface, 'All children must implement ' . $interface);

        $this->children = $children;

        return $this;
    }

    public function when(\Closure $whenFn): MenuItem
    {
        $this->whenFn = $whenFn;
        return $this;
    }

    public function build($user): ?array
    {
        $isVisible = $this->whenFn ? call_user_func($this->whenFn, $user) : true;

        if (! $isVisible) {
            return null;
        }

        return [
            'type'     => 'group',
            'label'    => $this->label,
            'icon'     => $this->icon,
            'children' => Collection::make($this->children)->map(
                fn(MenuItem $child) => $child->build($user)
            ),
        ];
    }
}
