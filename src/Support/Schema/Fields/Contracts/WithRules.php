<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

interface WithRules
{
    public function withRules(string $rules): self;

    public function getCreateRules(): array;

    public function withCreateRules($rules): self;

    public function getUpdateRules(): array;

    public function withUpdateRules($rules): self;
}
