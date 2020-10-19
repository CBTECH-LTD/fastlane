<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\UndefinedValue;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class FieldCollection extends Collection
{
    protected EntryType $entryType;

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
     * Set the entry type which owns this field collection.
     *
     * @param EntryType $entryType
     * @return $this
     */
    public function setEntryType(EntryType $entryType): self
    {
        $this->entryType = $entryType;

        return $this;
    }

    /**
     * Get the create rules of the fields.
     *
     * @param array $data
     * @return array
     */
    public function getCreateRules(array $data): array
    {
        return $this->getCollection()
            ->mapWithKeys(fn(Field $field) => $field->getCreateRules($data, $this->entryType))
            ->all();
    }

    /**
     * Get the update rules of the fields.
     *
     * @param array $data
     * @return array
     */
    public function getUpdateRules(array $data): array
    {
        return $this->getCollection()
            ->mapWithKeys(fn(Field $field) => $field->getUpdateRules($data, $this->entryType))
            ->all();
    }

    /**
     * Get the fields data with their complete set of information,
     * including field properties and configuration.
     *
     * @return array
     */
    public function getData(): array
    {
        $attributes = $this->getAttributes()->mapWithKeys(fn(Field $field) => [
            $field->getAttribute() => $field->read($this->entryType->modelInstance()->{$field->getAttribute()}, $this->entryType),
        ])->filter(fn($value) => ! $value instanceof UndefinedValue);

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

    public function flattenFields(): Collection
    {
        return Collection::make($this->items)
            ->mapWithKeys(function (Field $field) {
                if ($field instanceof Panel) {
                    return FieldCollection::make($field->getFields())
                        ->setEntryType($this->entryType)
                        ->all();
                }

                return [$field->getAttribute() => $field];
            });
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
        $items = Collection::make($this->items)->filter(function (Field $field) {
            return $field->setArrayFormat('listing')->isListable();
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
                $field->setArrayFormat('create');

                if ($field instanceof Panel) {
                    return FieldCollection::make($field->getFields())
                        ->setEntryType($this->entryType)
                        ->onCreate()
                        ->all();
                }

                return [$field->getAttribute() => $field];
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
                $field->setArrayFormat('update');

                if ($field instanceof Panel) {
                    return FieldCollection::make($field->getFields())
                        ->setEntryType($this->entryType)
                        ->onUpdate()
                        ->all();
                }

                return [$field->getAttribute() => $field];
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
            fn(Field $field) => ! $field instanceof Relationship
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
            fn(Field $field) => $field instanceof Relationship
        );
    }

    protected function newInstance(?array $data = null): FieldCollection
    {
        return tap(clone $this, function (FieldCollection $collection) use ($data) {
            if (is_array($data)) {
                $collection->items = $data;
            }
        });
    }
}
