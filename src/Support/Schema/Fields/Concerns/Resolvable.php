<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait Resolvable
{
    protected $resolveValueCallback;
    protected ?Collection $resolvedConfig = null;

    public function resolve(EntryTypeContract $entryType, EntryRequest $request): array
    {
        $this->resolvedConfig = Collection::make();

        $this->resolvedConfig->put('config', $this->resolveConfig($entryType, $request));
        $this->resolvedConfig->put('createRules', $this->resolveCreateRules($entryType, $request));
        $this->resolvedConfig->put('updateRules', $this->resolveUpdateRules($entryType, $request));

        return [$this];
    }

    public function resolveValueUsing($callback): self
    {
        $this->resolveValueCallback = $callback;
        return $this;
    }

    public function resolveValue(Model $model): array
    {
        if ($this->resolveValueCallback) {
            return call_user_func($this->resolveValueCallback, $model);
        }

        return [
            $this->getName() => $model->{$this->getName()},
        ];
    }

    protected function resolveCreateRules(EntryTypeContract $entryType, EntryRequest $request): array
    {
        $baseRules = $this->getBaseRules();

        $rules = array_merge([$baseRules], $this->getTypeRules(), [$this->createRules]);

        return [
            $this->getName() => Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|'),
        ];
    }

    protected function resolveUpdateRules(EntryTypeContract $entryType, EntryRequest $request): array
    {
        $baseRules = $this->getBaseRules();

        $rules = array_merge(['sometimes', $baseRules], $this->getTypeRules(), [$this->updateRules]);

        return [
            $this->getName() => Collection::make($rules)->filter(fn($r) => ! empty($r))->implode('|'),
        ];
    }

    protected function resolveConfig(EntryTypeContract $entryType, EntryRequest $request): array
    {
        return [];
    }
}
