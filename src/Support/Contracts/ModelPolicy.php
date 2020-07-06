<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ModelPolicy
{
    public function before($user, $ability);

    public function list($user);

    public function create($user);

    public function update($user, Model $model);

    public function delete($user, Model $office);
}
