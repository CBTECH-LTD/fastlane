<?php

declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Activable;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Hashable;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\RelatesToEntryType;
use CbtechLtd\Fastlane\Support\Eloquent\Contracts\LoadAttributesFromEntryType;
use CbtechLtd\Fastlane\Support\Eloquent\Contracts\LoadRelationshipsFromEntryType;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model implements Recordable, LoadAttributesFromEntryType, LoadRelationshipsFromEntryType
{
    use Hashable, RecordableTrait, Eventually, Activable, RelatesToEntryType;
}
