<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\UndefinedValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class FieldCollection extends Collection
{
    public static function make($items = []): FieldCollection
    {
        return new static($items);
    }

    public function __construct(array $items = [])
    {
        Assert::allIsAOf($items, Field::class, 'All items must be of type ' . Field::class);

        $items = Collection::make($items)->mapWithKeys(function (Field $field) {
            return [$field->getAttribute() => $field];
        })->all();

        parent::__construct($items);
    }

    /**
     * Get the create rules of the fields.
     *
     * @param Model $model
     * @param array $data
     * @return array
     */
    public function getCreateRules(Model $model, array $data): array
    {
        return $this->getAttributes()->flatMap->getCreateRules($model, $data)->toArray();
    }

    /**
     * Get the update rules of the fields.
     *
     * @param Model $model
     * @param array $data
     * @return array
     */
    public function getUpdateRules(Model $model, array $data): array
    {
        return $this->getAttributes()->flatMap->getUpdateRules($model, $data)->toArray();
    }

    /**
     * Get the fields data with their complete set of information,
     * including field properties and configuration.
     *
     * @param Model  $model
     * @param string $entryType
     * @return array
     */
    public function getData(Model $model, string $entryType): array
    {
        $attributes = $this->getAttributes()->mapWithKeys(fn (Field $field) => [
            $field->getAttribute() => $field->read($model, $entryType),
        ])->filter(fn ($value) => ! $value instanceof UndefinedValue);

        $relationships = $this->getRelationships()->map(function (Relationship $field) {
            // TODO: ONLY LOAD RELATIONSHIP IF IT'S IN A $with VARIABLE IN THE ENTRY TYPE INSTANCE
            //       SIMILAR TO THE WAY MODELS EAGER LOAD RELATIONSHIPS.

            $value = $this->entryType->modelInstance()->getRelationValue(
                $field->getRelationshipMethod()
            );

            return $field->read($value, $this->entryType);
        });

        return $attributes->merge($relationships)->toArray();
    }

    public function find(string $key): Field
    {
        return $this->flattenFields()->get($key);
    }

    public function getCollection(): Collection
    {
        return Collection::make($this->items);
    }

    public function flattenFields(): FieldCollection
    {
        $fields = Collection::make($this->items)
            ->flatMap(function (Field $field) {
                if ($field instanceof Panel) {
                    return $field->getFields()->all();
                }

                return [$field->getAttribute() => $field];
            });

        return $this->newInstance($fields->all());
    }

    public function panels(): FieldCollection
    {
        $items = Collection::make($this->items)->filter(function (Field $field) {
            return $field instanceof Panel;
        })->all();

        return $this->newInstance($items);
    }

    public function onListing(): FieldCollection
    {
        $items = Collection::make($this->items)
            ->flatMap(function (Field $field) {
                if (! $field->isListable()) {
                    return [];
                }

                if ($field instanceof Panel) {
                    return $field->getFields()->onListing();
                }

                return [$field->setArrayFormat('listing')];
            })->all();

        return $this->newInstance($items);
    }

    public function onCreate(): FieldCollection
    {
        $items = Collection::make($this->items)
            ->filter(function (Field $field) {
                return $field->isVisibleOnCreate();
            })
            ->mapWithKeys(function (Field $field) {
                return [$field->getAttribute() => $field->setArrayFormat('create')];
            })->all();

        return $this->newInstance($items);
    }

    public function onUpdate(): FieldCollection
    {
        $items = Collection::make($this->items)
            ->filter(function (Field $field) {
                return $field->isVisibleOnUpdate();
            })
            ->mapWithKeys(function (Field $field) {
                return [$field->getAttribute() => $field->setArrayFormat('update')];
            })->all();

        return $this->newInstance($items);
    }

    /**
     * Get the collection of items as a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getCollection()->toArray();
    }

    /**
     * Get only the field attributes.
     *
     * @return Collection
     */
    public function getAttributes(): Collection
    {
        return $this->flattenFields()->filter(
            fn (Field $field) => ! $field instanceof Relationship
        )->toBase();
    }

    /**
     * Get all computed attributes.
     *
     * @return Collection
     */
    public function getComputedAttributes(): Collection
    {
        return $this->getAttributes()->filter(
            fn (Field $field) => $field->isComputed()
        );
    }

    /**
     * Get only relationship names.
     *
     * @return Collection
     */
    public function getRelationships(): Collection
    {
        return $this->flattenFields()->filter(
            fn (Field $field) => $field instanceof Relationship
        );
    }

    protected function newInstance(
        ?array $data = null
    ): FieldCollection {
        return tap(clone $this, function (FieldCollection $collection) use ($data) {
            if (is_array($data)) {
                $collection->items = $data;
            }
        });
    }
}
