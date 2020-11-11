<?php

namespace CbtechLtd\Fastlane\View\Components;

use CbtechLtd\Fastlane\Fastlane;

class FlashMessages extends ReactiveComponent
{
    public array $messages = [];

    protected $listeners = ['fastlaneMessageAdded'];

    public function mount(): void
    {
        $this->messages = Fastlane::getFlashMessages();
    }

    public function fastlaneMessageAdded(array $message): void
    {
        $this->messages[] = $message;
    }

    public function render()
    {
        return view('fastlane::components.flash-messages');
    }
}
