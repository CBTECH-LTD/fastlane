<?php

namespace CbtechLtd\Fastlane\EntryTypes\Content;

use CbtechLtd\Fastlane\Repositories\Repository;

class ContentEntryRepository extends Repository
{
    public function __construct(Content $model)
    {
        $this->model = $model;
    }
}
