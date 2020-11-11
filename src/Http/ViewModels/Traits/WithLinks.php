<?php

namespace CbtechLtd\Fastlane\Http\ViewModels\Traits;

use CbtechLtd\Fastlane\Support\DataAccessor;
use Illuminate\Support\Collection;

trait WithLinks
{
    private array $links = [];

    /**
     * Add custom links to the view model.
     *
     * @param array $data
     * @return $this
     */
    public function withLinks(array $data): self
    {
        $this->links = array_merge($this->links, $data);
        return $this;
    }

    /**
     * Provide the links data to the view.
     *
     * @return DataAccessor
     */
    public function links(): DataAccessor
    {
        return once(fn() => $this->buildLinks());
    }

    /**
     * Build the links Data Accessor.
     *
     * @return DataAccessor
     */
    protected function buildLinks(): DataAccessor
    {
        $data = $this->getDefaultLinks()
            // Merge with links provided via withLinks method and transform to array.
            ->merge($this->links)
            ->map(function ($value) {
                return is_callable($value)
                    ? call_user_func($value, $this)
                    : $value;
            })->toArray();

        return new DataAccessor($data);
    }

    /**
     * A list of default links to be included in the links array.
     *
     * @return Collection
     */
    protected function getDefaultLinks(): Collection
    {
        return Collection::make();
    }
}
