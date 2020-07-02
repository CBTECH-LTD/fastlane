<?php

namespace CbtechLtd\Fastlane\Support\AccessControl;

abstract class ModelPolicy
{
    public function before($user, $ability)
    {
        //
    }

    abstract public function list($user);

    abstract public function create($user);

    abstract public function update($user, $model);

    abstract public function delete($user, $model);
}
