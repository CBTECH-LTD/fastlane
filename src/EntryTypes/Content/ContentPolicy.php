<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\Content;

use CbtechLtd\Fastlane\Support\Contracts\ModelPolicy;
use Illuminate\Database\Eloquent\Model;

class ContentPolicy implements ModelPolicy
{
    public function before($user, $ability)
    {
        if ($user->can(ContentEntryType::PERM_MANAGE_CONTENT)) {
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

    public function show($user, Model $model)
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
