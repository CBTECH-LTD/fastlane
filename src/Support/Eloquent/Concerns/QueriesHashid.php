<?php

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

trait QueriesHashid
{
    public function whereHashid(string $hashid): self
    {
        $this->where('id', $this->model->getHashidBuilder()->decode($hashid));
        return $this;
    }
}
