<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

interface WithCustomViews
{
    public function getIndexView(): ?string;

    public function getCreateView(): ?string;

    public function getEditView(): ?string;
}
