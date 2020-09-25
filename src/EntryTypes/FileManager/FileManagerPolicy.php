<?php

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\Support\Contracts\ModelPolicy;
use Illuminate\Database\Eloquent\Model;

class FileManagerPolicy implements ModelPolicy
{
    public function before($user, $ability)
    {
        if ($user->can(FileManagerEntryType::PERM_MANAGE_FILES)) {
            return true;
        }
    }

    public function list($user)
    {
        return false;
    }

    public function create($user)
    {
        return false;
    }

    public function view($user, Model $model)
    {
        return false;
    }

    public function update($user, Model $model)
    {
        return false;
    }

    public function delete($user, Model $office)
    {
        return false;
    }
}
