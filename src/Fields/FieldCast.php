<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class FieldCast implements CastsAttributes
{
    protected string $entryType;

    public function __construct(string $entryType)
    {
        $this->entryType = $entryType;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        return $this->field($key)->read($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $this->field($key)->write($value);
    }

    /**
     * @param string $key
     * @return Field
     */
    protected function field(string $key): Field
    {
        return FieldCollection::make($this->entryType::fields())
            ->flattenFields()
            ->get($key);
    }
}
