<?php

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Exceptions\ActiveStateException;
use Illuminate\Support\Arr;

trait Activable
{
    public function initializeActivable(): void
    {
        if (! in_array('is_active', $this->fillable)) {
            $this->fillable[] = 'is_active';
        }

        if (! Arr::has($this->casts, 'is_active')) {
            $this->casts['is_active'] = 'boolean';
        }
    }

    public function setActiveStateTo(bool $state): self
    {
        $this->attributes['is_active'] = $state;
        return $this;
    }

    public function activate(): self
    {
        if (! $this->update(['is_active' => true])) {
            throw ActiveStateException::couldNotActivate();
        }

        return $this;
    }

    public function deactivate(): self
    {
        if (! $this->update(['is_active' => false])) {
            throw ActiveStateException::couldNotDeactivate();
        }

        return $this;
    }

    public function setIsActiveAttribute(bool $state): self
    {
        return $this->setActiveStateTo($state);
    }
}
