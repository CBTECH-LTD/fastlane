<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\ValueResolver;

class BooleanTransformer implements Transformer
{
    /**
     * @inheritDoc
     */
    public function get(EntryType $entryType, $value)
    {
        return (bool)$value;
    }

    /**
     * @inheritDoc
     */
    public function set(EntryType $entryType, $value)
    {
        return (bool)$value;
    }

    public function fromRequest(EntryType $entryType, $value): ValueResolver
    {
        return new ValueResolver($entryType, (bool)$value);
    }
}
