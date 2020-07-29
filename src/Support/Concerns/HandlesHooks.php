<?php

namespace CbtechLtd\Fastlane\Support\Concerns;

use Illuminate\Pipeline\Pipeline;

trait HandlesHooks
{
    public function addHook(string $hook, $hookFunction): self
    {
        if (! isset($this->hooks[$hook])) {
            throw new \Exception('This class does not have hook ' . $hook);
        }

        $this->hooks[$hook][] = $hookFunction;

        return $this;
    }

    public function isHookEmpty(string $hook): bool
    {
        if (! isset($this->hooks[$hook])) {
            throw new \Exception('This class does not have hook ' . $hook);
        }

        return count($this->hooks[$hook]) === 0;
    }

    public function getHookFunctions(string $hook): array
    {
        if (! isset($this->hooks[$hook])) {
            throw new \Exception('This class does not have hook ' . $hook);
        }

        return $this->getHookFunctions($hook);
    }

    public function executeHooks(string $hook, $hookClass)
    {
        if ($this->isHookEmpty($hook)) {
            return $hookClass;
        }

        return app(Pipeline::class)
            ->send($hookClass)
            ->through($this->hooks[$hook])
            ->then(fn($hook) => $hook);
    }
}
