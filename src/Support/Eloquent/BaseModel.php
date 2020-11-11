<?php

declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Activable;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Hashable;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model implements Recordable
{
    use Hashable, RecordableTrait, Eventually, Activable;
}
