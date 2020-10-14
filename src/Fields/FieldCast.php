<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class FieldCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return $this->field($model, $key)->castValue($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $this->field($model, $key)->processValue($value);
    }

    /**
     * @param BaseModel $model
     * @param string    $key
     * @return Field
     */
    protected function field(BaseModel $model, string $key): Field
    {
        return $model->getEntryType()->getFields()->flattenFields()->get($key);
    }
}
