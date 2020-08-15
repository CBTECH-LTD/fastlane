<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\Support\ApiResources\EntryResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BackendUserResource extends EntryResource
{
    public function type(): string
    {
        return 'user';
    }

    public function attributes(Request $request): array
    {
        return [
            'name'      => $this->model->name,
            'email'     => $this->model->email,
            'is_active' => $this->model->is_active,
            'role'      => $this->model->role,
        ];
    }

    protected function relationships(): array
    {
        return [];
    }

    protected function links(): array
    {
        $links = Collection::make([
            ResourceLink::make('self', ['cp.backend-users.edit', $this->model]),
            ResourceLink::make('parent', ['cp.backend-users.index']),
        ]);

        return tap($links, function (Collection $l) {
            if (Auth::user()->is($this->model)) {
                $l->push(ResourceLink::make('profile', ['cp.account.profile']));
                $l->push(ResourceLink::make('security', ['cp.account.security']));
                $l->push(ResourceLink::make('user', ['cp.account.tokens.index']));
            }
        })->all();
    }

    protected function meta(): array
    {
        return [];
    }
}
