<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

class RelationshipCast extends FieldCast
{
    public function get($model, string $key, $value, array $attributes)
    {
        $field = $this->field($model, $key);
        $relationValue = $model->getRelationValue($key);

        return new Value($model->getEntryType(), $relationValue, $field);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        $this->field($model, $key)
            ->transformer()
            ->set($model->getEntryType(), $value);
    }
}
