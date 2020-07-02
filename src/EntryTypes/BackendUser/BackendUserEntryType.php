<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
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

    public function store(array $data): Model
    {
        $this->gate->authorize('create', $this->model());

        return tap($this->newModelInstance(), function ($user) use ($data) {
            $data['password'] = Str::random(16);

            $user
                ->fill($data)
                ->assignRole($data['role'])
                ->save();
        });
    }

    public function update(string $hashid, array $data): Model
    {
        $entry = User::findHashid($hashid);
        $this->gate->authorize('update', $entry);

        if ($role = Arr::get($data, 'role')) {
            $entry->assignRole($data['role']);
        }

        $entry->fill($data)->save();

        return $entry;
    }
}
