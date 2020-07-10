<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResourceCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BackendUserEntryType extends EntryType
{
    const PERM_MANAGE_SYSTEM_ADMINS = 'manage admins';
    const ROLE_SYSTEM_ADMIN = 'system admin';

    public function icon(): string
    {
        return 'user';
    }

    public function model(): string
    {
        return User::class;
    }

    public function getItems(): ApiResourceCollection
    {
        $this->gate->authorize('list', $this->model());

        $items = $this->newModelInstance()
            ->newModelQuery()
            ->except(Auth::user())
            ->get();

        return BackendUserResource::collection($items);
    }

    public function store(EntryRequest $request, array $data): Model
    {
        $this->gate->authorize('create', $this->model());

        return tap($this->newModelInstance(), function ($user) use ($request, $data) {
            foreach ($this->schema()->getDefinition()->toCreate() as $field) {
                $field->hydrateValue($request, $data[$field->getName()], $user);
            }

            $user->password = Str::random(16);

            $user
                ->assignRole($data['role'])
                ->save();
        });
    }

    public function update(EntryRequest $request, string $hashid, array $data): Model
    {
        $entry = User::findHashid($hashid);
        $this->gate->authorize('update', $entry);

        if ($role = Arr::get($data, 'role')) {
            $entry->assignRole($data['role']);
        }

        foreach ($this->schema()->getDefinition()->toCreate() as $field) {
            $field->hydrateValue($request, $data[$field->getName()], $entry);
        }

        $entry->save();
        return $entry;
    }
}
