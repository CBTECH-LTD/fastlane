<?php

declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent;

use Altek\Eventually\Eventually;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Activable;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Hashable;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    use Hashable, Eventually, Activable;
}
