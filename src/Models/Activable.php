<?php

namespace CbtechLtd\Fastlane\Models;

use CbtechLtd\Fastlane\Exceptions\ActiveStateException;

trait Activable
{
    public function initializeActivable(): void
    {
        $this
            ->mergeFillable(['is_active'])
            ->mergeCasts(['is_active' => 'boolean']);
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
