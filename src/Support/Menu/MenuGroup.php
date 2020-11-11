<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuItem;
use CbtechLtd\Fastlane\View\Components\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webmozart\Assert\Assert;

class MenuGroup extends MenuItem
{
    public string $label;
    public array $children = [];
    private ?\Closure $whenFn = null;

    public function __construct(string $label, array $children = [])
    {
        $this->label = $label;
        $this->setChildren($children);
    }

    public static function make(string $label, array $children = []): MenuGroup
    {
        return new static($label, $children);
    }

    public function setChildren(array $children): self
    {
        $interface = Component::class;
        Assert::allIsInstanceOf($children, $interface, 'All children must extend ' . $interface);

        $this->children = Collection::make($children)->filter(fn(Component $child) => $child->shouldRender())->all();
        return $this;
    }


    public function when(\Closure $whenFn): self
    {
        $this->whenFn = $whenFn;
        return $this;
    }

    public function render()
    {
        return view('fastlane::components.menu-group', [
            'children' => $this->children,
            'label'    => $this->label,
        ]);
    }

    public function shouldRender()
    {
        return $this->whenFn ? call_user_func($this->whenFn, Auth::user()) : true;
    }

    public function getGroup(): string
    {
        return '';
    }
}
