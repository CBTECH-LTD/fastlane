<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class BlockEditorCast implements CastsAttributes
{
    private string $entryType;

    public function __construct(string $entryType)
    {
        $this->entryType = $entryType;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        dd('get', $value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        dd('set', $value);
    }
}
