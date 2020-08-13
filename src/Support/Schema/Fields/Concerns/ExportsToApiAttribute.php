<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait ExportsToApiAttribute
{
    protected ?Closure $toApiAttributeCallback = null;

    public function exportToApiAttributeUsing(?Closure $callback = null)
    {
        $this->toApiAttributeCallback = $callback;
        return $this;
    }

    public function toApiAttribute(Model $model, array $options = [])
    {
        if ($this->toApiAttributeCallback) {
            return call_user_func($this->toApiAttributeCallback, $model);
        }

        return $this->resolveValue($model);
    }
}
