<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

interface EntryInstance extends Arrayable
{
    public function id();

    public function title(): string;

    public function type(): EntryType;

    public function model(): Model;

    public function schema(): EntrySchema;

    public function saveModel(): self;
}
