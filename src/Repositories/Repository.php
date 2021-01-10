<?php

namespace CbtechLtd\Fastlane\Repositories;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Exceptions\DeleteEntryException;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Hashable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

abstract class Repository
{
    /** @var EntryType | string */
    protected string $entryType;

    /** @var BaseModel */
    protected BaseModel $model;

    /** @var string */
    protected string $defaultOrder = 'created_at:desc';

    /** @var string */
    protected string $key;

    public function setEntryType(string $entryType): self
    {
        $this->entryType = $entryType;
        return $this;
    }

    /**
     * Get the underlying model.
     *
     * @return BaseModel
     */
    public function getModel(): BaseModel
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
        // Get the columns we want to retrieve from the database.
        $queryCols = ['id'];

        if (in_array(Hashable::class, class_uses_recursive($this->model))) {
            $queryCols[] = 'hashid';
        }

        // Initialize the model query builder.
        $query = $this->model->newQuery()->select(
            $this->getColumnListing(array_merge($queryCols, $columns))
        );

        if ($orderBy = Arr::get($filters, 'order', $this->defaultOrder)) {
            $query->orderBy(...explode(':', $orderBy));
        }

        $this->beforeFetchListing($query);

        $result = is_null($perPage)
            ? $query->get()
            : $query->paginate($perPage);

        return $this->processListingResult($result);
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
    public function findOne($id, $columns = ['*']): ?Model
    {
        return $this->getModel()->newModelQuery()
            ->select($columns)
            ->where($this->getKey(), $id)
            ->first();
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
        return $this->getModel()->newModelQuery()
            ->select($columns)
            ->whereIn($this->getKey(), $ids)
            ->get();
    }

    /**
     * Find one entry or throw a 404 error.
     *
     * @param string|int $id
     * @param string[]   $columns
     * @return BaseModel
     */
    public function findOrFail($id, $columns = ['*']): BaseModel
    {
        return $this->getModel()->newModelQuery()
            ->select($columns)
            ->where($this->getKey(), $id)
            ->firstOrFail();
    }

    /**
     * Create a new instance of the model.
     *
     * @param array $data
     * @return BaseModel
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(array $data): Model
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
     * @param       $id
     * @param array $data
     * @return BaseModel
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($id, array $data): Model
    {
        $model = $this->findOrFail($id);

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
     * @param $id
     * @return BaseModel
     * @throws DeleteEntryException
     */
    public function delete($id): BaseModel
    {
        $model = $this->findOrFail($id);

        if (! $model->delete()) {
            throw new DeleteEntryException;
        }

        return $model;
    }

    /**
     * This method is called just before the listing query is
     * executed. It's a good place to customize the query instead
     * of messing with all the base query provided by Fastlane.
     *
     * @param Builder $query
     */
    protected function beforeFetchListing(Builder $query): void
    {
        //
    }

    /**
     * Process the result of the listing query.
     * It's a good place to do some custom operation before
     * returning the list.
     *
     * @param Collection|LengthAwarePaginator $result
     * @return \Illuminate\Support\Collection|Collection|LengthAwarePaginator
     */
    protected function processListingResult($result)
    {
        return $result;
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
     * @param BaseModel       $model
     * @param FieldCollection $fields
     * @param array           $data
     * @return void
     */
    protected function beforeSave(BaseModel $model, FieldCollection $fields, array $data): void
    {
        //
    }

    /**
     * This method is called right after the model has been saved.
     * It's useful, for example, to handle relationships.
     *
     * @param BaseModel       $model
     * @param FieldCollection $fields
     * @param array           $data
     */
    protected function afterSave(BaseModel $model, FieldCollection $fields, array $data): void
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
