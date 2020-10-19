<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\ContentBlocks;

use Illuminate\Support\Collection;

class ContentBlockCollection extends Collection
{
    private bool $shallow = false;

    public function shallow(): self
    {
        $this->shallow = true;
        return $this;
    }

    public function toArray()
    {
        return $this->mapWithKeys(function ($class) {
            return [
                $class::key() => $class::make()->shallow($this->shallow),
            ];
        });
    }
}
