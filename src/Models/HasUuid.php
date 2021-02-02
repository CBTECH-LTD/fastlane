<?php

namespace CbtechLtd\Fastlane\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $m) {
            $m->uuid = Str::uuid()->toString();
        });
    }

    public function initializeHasUuid(): void
    {
        $this->mergeFillable(['uuid']);
    }
}
