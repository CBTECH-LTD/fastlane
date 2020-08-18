<?php

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuItem;

class MenuLink implements MenuItem
{
    private string $href;
    private string $label;
    private string $group = '';
    private ?string $icon = null;
    private ?\Closure $whenFn = null;

    public function __construct(string $href, string $label)
    {
        $this->href = $href;
        $this->label = $label;
    }

    public static function make(string $href, string $label): self
    {
        return new static($href, $label);
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function group(string $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function when(\Closure $whenFn): self
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
            'type'  => 'link',
            'group' => $this->group,
            'href'  => $this->href,
            'label' => $this->label,
            'icon'  => $this->icon,
        ];
    }
}
