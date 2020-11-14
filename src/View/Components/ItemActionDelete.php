<?php

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ItemActionDelete extends ReactiveComponent
{
    public string $entryType;
    public string $entryId;
    public string $url;
    public string $redirect;

    public function mount(string $entryType, string $entryId, string $url, string $redirect)
    {
        $this->entryType = $entryType;
        $this->entryId = $entryId;
        $this->url = $url;
        $this->redirect = $redirect;
    }

    /**
     * Delete the entry.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function confirmAction()
    {
        $entry = $this->entryType::repository()->findOrFail($this->entryId);

        if (Gate::getPolicyFor($this->entryType::repository()->getModel())) {
            Gate::forUser(Auth::guard('fastlane-cp')->user())->authorize('delete', $entry);
        }

        $this->entryType::repository()->delete($this->entryId);

        return redirect($this->redirect);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('fastlane::components.item-action-delete');
    }
}
