<?php

namespace CbtechLtd\Fastlane\Contracts;

interface WithCustomViews
{
    public function getListingView(): ?string;

    public function getCreateView(): ?string;

    public function getEditView(): ?string;
}
