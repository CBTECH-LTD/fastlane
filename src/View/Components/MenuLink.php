<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MenuLink extends Component
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
        if (Str::startsWith(request()->path(), $this->href)) {
            return 'text-gray-200 bg-gray-800';
        }

        return 'bg-transparent hover:bg-gray-300 text-gray-600 hover:text-gray-700';
    }
}
