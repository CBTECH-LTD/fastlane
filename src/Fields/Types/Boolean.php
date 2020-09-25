<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Transformers\BooleanTransformer;

class Boolean extends Field
{
    protected string $component = 'toggle';

    public function transformer(): Transformer
    {
        return new BooleanTransformer;
    }

    protected function getFieldRules(array $data, EntryType $entryType): array
    {
        return [$this->getAttribute() => 'boolean'];
    }
}
