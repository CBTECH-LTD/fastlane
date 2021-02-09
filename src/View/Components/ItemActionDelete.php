<?php

namespace CbtechLtd\Fastlane\View\Components;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Fastlane;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ItemActionDelete extends ReactiveComponent
{
    public EntryInstance $entry;
    public string $redirect;

    /**
     * These rules are needed because Livewire requires
     * that rules must be defined for public models.
     * It's weird, but otherwise it does not work.
     *
     * @var \string[][]
     */
    protected $rules = [
        'entry.entry_type' => ['required'],
        'entry.entry_id' => ['required'],
        'entry.for' => ['required'],
    ];

    public function mount(EntryInstance $entry, string $redirect)
    {
        $this->entry = $entry;
        $this->redirect = $redirect;
    }

    /**
     * Delete the entry.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \CbtechLtd\Fastlane\Exceptions\DeleteEntryException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function confirmAction()
    {
        if (Gate::getPolicyFor($this->entry->model())) {
            Gate::forUser(Auth::guard('fastlane-cp')->user())->authorize('delete', $this->entry->model());
        }

        $this->entry->type()::repository()->delete($this->entry);

        Fastlane::flashSuccess(
            __('fastlane::core.flash.deleted', ['name' => $this->entry->type()::label()['singular']]),
            'trash',
            $this
        );

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
