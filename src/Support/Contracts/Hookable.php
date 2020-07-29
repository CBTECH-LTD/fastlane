<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

interface Hookable
{
    public function addHook(string $hook, $hookFunction);

    public function isHookEmpty(string $hook): bool;

    public function getHookFunctions(string $hook): array;

    public function executeHooks(string $hook, $hookClass);
}
