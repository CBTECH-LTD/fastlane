<?php

declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Activable;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Hashable;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\RelatesToEntryType;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model implements Recordable
{
    use Hashable, RecordableTrait, Eventually, Activable, RelatesToEntryType;

    /**
     * This method provide us a string representation
     * of the model instance.
     *
     * @return string
     */
    public function toString(): string
    {
        // Let's use by default the first field defined in the Entry Schema.
        $field = $this->getEntryType()
             ->schema()
             ->getDefinition()
             ->getFields()[0]
            ->getName();

        return $this->{$field};
    }
}
