<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MenuLink extends MenuItem
{
    public string $href;
    public string $label;
    public ?string $icon = null;
    protected string $group = '';
    protected ?\Closure $whenFn = null;

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

    public function render()
    {
        return view('fastlane::components.menu-link', [
            'href'    => $this->href,
            'label'   => $this->label,
            'icon'    => $this->icon,
            'classes' => $this->buildLinkClasses(),
        ]);
    }

    public function shouldRender()
    {
        return $this->whenFn ? call_user_func($this->whenFn, Auth::user()) : true;
    }

    protected function buildLinkClasses(): string
    {
        if (Str::startsWith(request()->url(), $this->href)) {
            return 'text-brand-800 bg-brand-100';
        }

        return 'bg-transparent text-gray-600 hover:bg-gray-300 hover:text-gray-800';
    }
}
