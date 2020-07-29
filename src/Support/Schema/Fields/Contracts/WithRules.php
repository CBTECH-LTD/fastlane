<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

interface WithRules
{
    public function setRules(string $rules): self;

    public function getCreateRules(): array;

    public function setCreateRules(string $rules): self;

    public function getUpdateRules(): array;

    public function setUpdateRules(string $rules): self;
}
