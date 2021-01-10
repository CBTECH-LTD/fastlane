<?php

namespace CbtechLtd\Fastlane\EntryTypes\Content;

use CbtechLtd\Fastlane\Repositories\Repository;

class ContentRepository extends Repository
{
    public function __construct(Content $model)
    {
        $this->model = $model;
    }
}
