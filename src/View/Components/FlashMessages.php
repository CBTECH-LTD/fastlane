<?php

namespace CbtechLtd\Fastlane\View\Components;

use CbtechLtd\Fastlane\Fastlane;
use Illuminate\Support\Collection;

class FlashMessages extends ReactiveComponent
{
    public array $messages = [];

    protected $listeners = ['fastlaneMessagesUpdated'];

    public function mount(): void
    {
        $this->messages = Fastlane::getFlashMessages();
    }

    public function fastlaneMessagesUpdated(array $msgs): void
    {
        $this->messages = Collection::make($msgs)->map(fn ($m) => (object) $m)->all();
    }

    public function render()
    {
        return view('fastlane::components.flash-messages');
    }
}
