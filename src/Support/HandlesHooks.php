<?php

namespace CbtechLtd\Fastlane\Support;

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

    protected function isHookEmpty(string $hook): bool
    {
        if (! isset($this->hooks[$hook])) {
            throw new \Exception('This class does not have hook ' . $hook);
        }

        return count($this->hooks[$hook]) === 0;
    }

    protected function getHookFunctions(string $hook): array
    {
        if (! isset($this->hooks[$hook])) {
            throw new \Exception('This class does not have hook ' . $hook);
        }

        return $this->getHookFunctions($hook);
    }

    protected function executeHooks(string $hook, $hookClass)
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
