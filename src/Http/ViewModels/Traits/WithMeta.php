<?php

namespace CbtechLtd\Fastlane\Http\ViewModels\Traits;

use CbtechLtd\Fastlane\Support\DataAccessor;
use Illuminate\Support\Collection;

trait WithMeta
{
    private array $meta = [];

    /**
     * Add custom meta to the view model.
     *
     * @param array $data
     * @return $this
     */
    public function withMeta(array $data): self
    {
        $this->meta = array_merge($this->meta, $data);
        return $this;
    }

    /**
     * Provide the meta data to the view.
     *
     * @return DataAccessor
     */
    public function meta(): DataAccessor
    {
        return once(fn() => $this->buildMeta());
    }

    /**
     * Build the meta data accessor.
     *
     * @return DataAccessor
     */
    protected function buildMeta(): DataAccessor
    {
        $data = $this->getDefaultMeta()->merge([
            'entryType' => [
                'class'      => $this->entryType,
                'identifier' => $this->entryType::key(),
                'label'      => $this->entryType::label(),
                'routes'     => $this->entryType::routes(),
                'schema'     => $this->buildSchemaForMeta(),
            ],
        ])
        ->merge($this->meta)
        ->map(function ($value) {
            return is_callable($value)
                ? call_user_func($value, $this)
                : $value;
        })->toArray();

        return new DataAccessor($data);
    }

    protected function getDefaultMeta(): Collection
    {
        return Collection::make();
    }

    /**
     * @return mixed
     */
    protected function buildSchemaForMeta()
    {
        return [];
    }
}
