<?php

namespace CbtechLtd\Fastlane\Support\ControlPanelResources;

use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Http\Request;

class TokenResource extends ResourceType
{
    public function type(): string
    {
        return 'personal-access-token';
    }

    public function attributes(Request $request): array
    {
        return [
            'name' => $this->model->name,
            'last_used_at' => $this->model->last_used_at,
            'abilities' => $this->model->abilities,
        ];
    }

    protected function links(): array
    {
        return [
            ResourceLink::make('self', ['cp.account.tokens.destroy', $this->model]),
            ResourceLink::make('top', ['cp.account.tokens.index']),
        ];
    }


}
