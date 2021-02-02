<?php

namespace CbtechLtd\Fastlane\Repositories;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Exceptions\DeleteEntryException;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Models\Entry;
use CbtechLtd\Fastlane\Models\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class EntryRepository
{
    /** @var EntryType | string */
    protected string $entryType;

    /** @var Entry */
    protected Entry $model;

    /** @var string */
    protected string $key;

    public function __construct(string $entryType)
    {
        $this->setEntryType($entryType);
        $this->model = (new Entry)->setEntryType($entryType);
    }

    public function setEntryType(string $entryType): self
    {
        $this->entryType = $entryType;
        return $this;
    }

    public function newQuery(): RepositoryQuery
    {
        return new RepositoryQuery($this->entryType, $this->model->newQuery());
    }

    /**
     * Get the underlying model.
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        if (! isset($this->key)) {
            return $this->getModel()->getRouteKeyName();
        }

        return $this->key;
    }

    public function getDefaultOrder(): string
    {
        return $this->entryType::listingDefaultOrder();
    }

    /**
     * Get a list of items. Set $perPage argument to null if
     * pagination is not required.
     *
     * @param array    $columns
     * @param array    $filters
     * @param int|null $perPage
     * @return Collection|LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function get(array $columns = [], array $filters = [], ?int $perPage = 20)
    {
        $filters = array_merge([
            'order' => $this->getDefaultOrder(),
        ], $filters);

        return $this->newQuery()->get($columns, $filters, $perPage);
    }

    /**
     * Create a new instance of the underlying model
     * but does not save it.
     *
     * @param array $data
     * @return Model
     */
    public function newModel(array $data = []): Model
    {
        return $this->getModel()->newInstance($data);
    }

    /**
     * Find a single entry.
     *
     * @param          $id
     * @param string[] $columns
     * @return Model|null
     */
    public function findOne($id, $columns = ['*']): ?EntryInstance
    {
        return $this->newQuery()->findOne($id, $columns);
    }

    /**
     * Fetch the provided IDs.
     *
     * @param array    $ids
     * @param string[] $columns
     * @return Collection
     */
    public function findMany(array $ids, $columns = ['*']): Collection
    {
        return $this->newQuery()->findMany($ids, $columns);
    }

    /**
     * Find one entry or throw a 404 error.
     *
     * @param string|int $id
     * @param string[]   $columns
     * @return EntryInstance
     */
    public function findOrFail($id, $columns = ['*']): EntryInstance
    {
        return $this->newQuery()->findOrFail($id, $columns);
    }

    /**
     * Create a new instance of the model.
     *
     * @param array $data
     * @return Entry
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(array $data): Entry
    {
        // Initialize an instance of the model.
        $model = $this->newModel();

        $fields = FieldCollection::make($this->entryType::fields())->onCreate();

        // Validate the request data against the proper fields
        // then fill the model with such data and finally save it.
        $this->fillModel($model, $fields, $fields->getCreateRules($model, $data), $data);
        $this->saveModel($model, $fields, $data);

        return $model;
    }

    /**
     * Update an existent instance of the underlying model
     * with the given dataset.
     *
     * @param Entry $model
     * @param array $data
     * @return Entry
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Entry $model, array $data): Entry
    {
        $fields = FieldCollection::make($this->entryType::fields())->onUpdate();

        // Validate the request data against the proper fields
        // then fill the model with such data and finally save it.
        $this->fillModel($model, $fields, $fields->getUpdateRules($model, $data), $data);
        $this->saveModel($model, $fields, $data);

        return $model;
    }

    /**
     * Delete the entry model matching the given id.
     *
     * @param EntryInstance $entry
     * @return EntryInstance
     * @throws DeleteEntryException
     */
    public function delete(EntryInstance $entry): EntryInstance
    {
        if (! $entry->model()->delete()) {
            throw new DeleteEntryException;
        }

        return $entry;
    }

    /**
     * Set the fillable attributes of the model.
     *
     * @param Model           $model
     * @param FieldCollection $fields
     * @param array           $rules
     * @param array           $data
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function fillModel(Model $model, FieldCollection $fields, array $rules, array $data): void
    {
        $data = Validator::make($data, $rules)->validated();

        $fields->flattenFields()->getCollection()->each(function (Field $field) use ($model, $data) {
            $field->write($model, $this->entryType, Arr::get($data, $field->getAttribute()));
        });
    }

    /**
     * This method is called before saving the model.
     *
     * @param Entry           $model
     * @param FieldCollection $fields
     * @param array           $data
     * @return void
     */
    protected function beforeSave(Entry $model, FieldCollection $fields, array $data): void
    {
        //
    }

    /**
     * This method is called right after the model has been saved.
     * It's useful, for example, to handle relationships.
     *
     * @param Entry           $model
     * @param FieldCollection $fields
     * @param array           $data
     */
    protected function afterSave(Entry $model, FieldCollection $fields, array $data): void
    {
        //
    }

    /**
     * Save the given model.
     *
     * @param                 $model
     * @param FieldCollection $fields
     * @param array           $data
     */
    public function saveModel($model, FieldCollection $fields, array $data): void
    {
        $this->beforeSave($model, $fields, $data);
        $model->save();
        $this->afterSave($model, $fields, $data);
    }

    /**
     * Get a list of columns defined in the database table.
     *
     * @param array|null $only
     * @return array
     */
    public function getColumnListing(?array $only = null): array
    {
        return collect($this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable()))
            ->mapWithKeys(fn($c) => [$c => $c])
            ->when($only, fn($collection, $onlyCols) => $collection->only($onlyCols))
            ->values()->all();
    }
}
