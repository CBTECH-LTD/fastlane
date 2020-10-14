<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

class RelationshipCast extends FieldCast
{
    public function get($model, string $key, $value, array $attributes)
    {
        return $this->field($model, $key)->castValue($model->getRelationValue($key));
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $this->field($model, $key)->processValue($value);
    }
}
