<?php

namespace CbtechLtd\Fastlane\View\Components;

class ItemActionDelete extends ReactiveComponent
{
    public string $url;
    public string $redirect;

    public function mount(string $url, string $redirect)
    {
        $this->url = $url;
        $this->redirect = $redirect;
    }

    public function confirmAction()
    {
        return redirect($this->redirect);
    }

    public function render()
    {
        return view('fastlane::components.item-action-delete');
    }
}
