<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\Content;

class QueryBuilder extends \CbtechLtd\Fastlane\EntryTypes\QueryBuilder
{
    public function withSlug(string $slug): self
    {
        $this->builder->where('slug', $slug);
        return $this;
    }
}
