<?php

namespace CbtechLtd\Fastlane\Support\Eloquent;

use CbtechLtd\Fastlane\Support\Eloquent\Concerns\QueriesHashid;
use Illuminate\Database\Eloquent\Builder;

class BaseQueryBuilder extends Builder
{
    use QueriesHashid;
}
