<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Http\Request;

class BackendUserResource extends ResourceType
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
        return [
            ResourceLink::make('self', ['cp.backend-users.edit', $this->model]),
            ResourceLink::make('parent', ['cp.backend-users.index']),
        ];
    }

    protected function meta(): array
    {
        return [];
    }
}
