<?php

namespace CbtechLtd\Fastlane\Models;

trait HasDynamicRelations
{
    protected array $dynamicRelations = [];

    /**
     * Add a dynamic relationship to the model.
     *
     * @param string   $name
     * @param callable $closure
     */
    public function addDynamicRelation(string $name, callable $closure): void
    {
        $this->dynamicRelations[$name] = $closure;
    }

    /**
     * Determine whether a relation exists in the dynamic relationship list.
     *
     * @param string $name
     * @return bool
     */
    public function hasDynamicRelation(string $name): bool
    {
        return array_key_exists($name, $this->dynamicRelations);
    }

    /**
     * If the key exists in relations then return call to relation or else
     * return the call to the parent
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->hasDynamicRelation($name)) {
            if ($this->relationLoaded($name)) {
                return $this->relations[$name];
            }

            return $this->getRelationshipFromMethod($name);
        }

        return parent::__get($name);
    }

    /**
     * If the method exists in relations then return the relation or else
     * return the call to the parent
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (static::hasDynamicRelation($name)) {
            return call_user_func($this->dynamicRelations[$name], $this);
        }

        return parent::__call($name, $arguments);
    }
}
