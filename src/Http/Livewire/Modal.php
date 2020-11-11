<?php

namespace CbtechLtd\Fastlane\Http\Livewire;

use Livewire\Component;

class Modal extends Component
{
    public bool $show;
    public string $title;

    public function mount(bool $show, string $title)
    {
        $this->show = $show;
        $this->title = $title;
    }

    public function render()
    {
        return view('fastlane::modal');
    }
}
