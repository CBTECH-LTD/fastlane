<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\ContentBlocks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ContentBlockCollection extends Collection
{
    private Collection $resolvedFields;
    private bool $shallow = false;
    private Model $model;

    public function shallow(): self
    {
        $this->shallow = true;
        return $this;
    }

    public function withModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function toArray()
    {
        $this->resolveFields()->each(function (\CbtechLtd\Fastlane\Contracts\ContentBlock $block) {
            $block->shallow($this->shallow);
        });

        return $this->resolvedFields;
    }

    public function getResolved()
    {
        $this->resolveFields()->each(function (\CbtechLtd\Fastlane\Contracts\ContentBlock $block) {
            $block->shallow($this->shallow);
        });

        return $this->resolvedFields;
    }

    private function resolveFields(): Collection
    {
        if (! isset($this->resolvedFields)) {
            $this->resolvedFields = $this->mapWithKeys(function ($class) {
                return [
                    $class::key() => $class::make(),
                ];
            });
        }

        return $this->resolvedFields;
    }
}
