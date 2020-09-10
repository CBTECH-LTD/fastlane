<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class BlockItemFieldValue extends FieldValue
{
    public function get(string $data, $default = null)
    {
        return data_get($this->value, $data, $default);
    }

    public function block(): string
    {
        return $this->value['block'];
    }

    public function uuid(): string
    {
        return $this->value['uuid'];
    }

    public function data(): array
    {
        return $this->value['data'];
    }
}
