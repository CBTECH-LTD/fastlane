<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\Transformable;
use Illuminate\Contracts\Support\Arrayable;

class Value implements Arrayable
{
    protected $raw;
    protected ?Transformable $transformable;
    private EntryType $entryType;

    public function __construct(EntryType $entryType, $value, ?Transformable $transformable = null)
    {
        $this->raw = $value;
        $this->transformable = $transformable;
        $this->entryType = $entryType;
    }

    public function entryType(): EntryType
    {
        return $this->entryType;
    }

    public function raw()
    {
        return $this->raw;
    }

    public function value()
    {
        if (! $this->transformable) {
            return $this->raw;
        }

        return $this->transformable
            ->transformer()
            ->get($this->entryType, $this->raw);
    }

    public function field(): ?Transformable
    {
        return $this->transformable;
    }

    public function __toString()
    {
        return (string)$this->value();
    }

    public function toArray()
    {
        return [
            'value' => $this->value(),
            'field' => $this->field()->toArray(),
        ];
    }
}
