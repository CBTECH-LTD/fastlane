<?php

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

trait Hashable
{
    protected Hashids $hashidBuilder;

    public static function bootHashable()
    {
        static::created(function (Model $model) {
            $model->hashid = $model->hashidBuilder->encode($model->getKey());
            $model->save();
        });
    }

    public function initializeHashable(): void
    {
        $this->hashidBuilder = new Hashids(get_class($this), config('cms.hashid_min_length'));
    }

    public function getHashidBuilder(): Hashids
    {
        return $this->hashidBuilder;
    }

    public function getRouteKeyName()
    {
        return 'hashid';
    }

    public static function findHashid(string $hashid)
    {
        return static::where('hashid', $hashid)->firstOrFail();
    }
}
