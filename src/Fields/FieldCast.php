<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

use CbtechLtd\Fastlane\Contracts\Transformable;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class FieldCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        $field = $this->field($model, $key);

        return new Value(
            $model->getEntryType(),
            $value,
            $field instanceof Transformable ? $field : null
        );
    }

    public function set($model, string $key, $value, array $attributes)
    {
        $field = $this->field($model, $key);

        return ($field instanceof Transformable)
            ? $field->transformer()->set($model->getEntryType(), $value)
            : $value;
    }

    /**
     * @param BaseModel $model
     * @param string    $key
     * @return Field
     */
    protected function field(BaseModel $model, string $key): Field
    {
        return $model->getEntryType()->getFields()->find($key);
    }
}
