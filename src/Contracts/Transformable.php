<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Contracts;

interface Transformable
{
    public function transformer(): Transformer;
}
