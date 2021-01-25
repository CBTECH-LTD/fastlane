<?php

declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use CbtechLtd\Fastlane\Models\Activable;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model implements Recordable
{
    use RecordableTrait, Eventually, Activable;
}
