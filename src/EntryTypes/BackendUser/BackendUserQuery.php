<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Hashable;
use Illuminate\Support\Facades\Gate;

class BackendUserQuery
{
    public string $model = User::class;

    public function listing()
    {
        Gate::authorize('index', $this->model);

        // Start the query, selecting only fields we really need.
        $defaultColumns = ['id'];

        if (in_array(Hashable::class, class_uses_recursive(static::model()))) {
            $defaultColumns[] = 'hashid';
        }


    }
}
